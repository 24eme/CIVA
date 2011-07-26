<?php

abstract class DeclarationSecurityUser extends TiersSecurityUser {
    
    const CREDENTIAL_DECLARATION_EN_COURS = 'declaration_en_cours';
    const CREDENTIAL_DECLARATION_VALIDE = 'declaration_valide';
    const CREDENTIAL_ETAPE_EXPLOITATION = 'declaration_etape_exploitation';
    const CREDENTIAL_ETAPE_RECOLTE = 'declaration_etape_recolte';
    const CREDENTIAL_ETAPE_VALIDATION = 'declaration_etape_validation';
    
    protected $_etapes_credentials = array(DR::ETAPE_EXPLOITATION => self::CREDENTIAL_ETAPE_EXPLOITATION, 
                                           DR::ETAPE_RECOLTE      => self::CREDENTIAL_ETAPE_RECOLTE, 
                                           DR::ETAPE_VALIDATION   => self::CREDENTIAL_ETAPE_VALIDATION);
    
    protected $_credentials_declaration = array(self::CREDENTIAL_DECLARATION_EN_COURS, 
                                                self::CREDENTIAL_DECLARATION_VALIDE,
                                                self::CREDENTIAL_ETAPE_EXPLOITATION, 
                                                self::CREDENTIAL_ETAPE_RECOLTE, 
                                                self::CREDENTIAL_ETAPE_VALIDATION);
    
    protected $_declaration = null;
    
    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options 
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);
        
        if (!$this->isAuthenticated())
        {
            $this->signOutDeclaration();
        }
    }
    
    /**
    * 
    */
    protected function clearCredentialsDeclaration() {
        foreach($this->_credentials_declaration as $credential) {
            $this->removeCredential($credential);
        }
    }
    
    /**
     * 
     */
    public function signOutDeclaration() {
        $this->_declaration = null;
        $this->clearCredentialsDeclaration();
    }
    
    /**
     * @return DR
     */
    public function getDeclaration() {
        $this->requireDeclaration();
        $this->requireTiers();
        if (is_null($this->_declaration)) {
            $this->_declaration = $this->getTiers()->getDeclaration($this->getCampagne());
            if (!$this->_declaration) {
                $declaration = new DR();
                $declaration->set('_id', 'DR-'.$this->getTiers()->cvi.'-'.$this->getCampagne());
                return $declaration;
            }
        }

        return $this->_declaration;
    }
    
    /**
     * @return string
     */
    public function getCampagne() {
      return CurrentClient::getCurrent()->year;
    }

    /**
     *
     * @param string $etape 
     */
    public function addEtapeDeclaration($etape) {
        $this->requireDeclaration();
        if ($etape == 'recolte') {
            
        }
        if ($this->getDeclaration()->addEtape($etape)) {
            $this->getDeclaration()->save();
            $this->addCredentialsEtapeDeclaration();
        }
    }
    
    /**
     *
     */
    protected function addCredentialsEtapeDeclaration() {
        $declaration = $this->getDeclaration();
        if ($declaration->exist('etape') && $declaration->etape) {
            $this->addCredential($this->_etapes_credentials[$declaration->etape]);
            foreach(DR::$_etapes_inclusion[$declaration->etape] as $etape) {
                $this->addCredential($this->_etapes_credentials[$etape]);
            }
        }
    }
    /**
     * returns trus if editable
     */
    public function isDrEditable() {
      if(ConfigurationClient::getConfiguration($this->getCampagne())->exist('dr_non_editable'))
	return ! ConfigurationClient::getConfiguration()->dr_non_editable;
      return 1;
    }

    /**
     * 
     */
    public function initCredentialsDeclaration() {
        $this->requireDeclaration();
        $declaration = $this->getDeclaration();
        $this->clearCredentialsDeclaration();
        if ($this->isDrEditable()) {
            if ($declaration->isValideeTiers() || $declaration->isValideeCiva()) {
                $this->addCredential(self::CREDENTIAL_DECLARATION_VALIDE);
            } else {
                $this->addCredential(self::CREDENTIAL_DECLARATION_EN_COURS);
                $this->addCredentialsEtapeDeclaration();
            }
        }
    }
    
    /**
     * 
     */
    protected function requireDeclaration() {
        $this->requireTiers();
        if (!$this->hasCredential(self::CREDENTIAL_DECLARATION)) {
            throw new sfException("you must be logged in with a tiers");
        }
    }
    
    /**
     *
     * @param _Tiers $tiers 
     */
    public function signInTiers($tiers) {
        parent::signInTiers($tiers);
        if($this->hasCredential(myUser::CREDENTIAL_DECLARATION)) {
            $this->initCredentialsDeclaration();
        }
    }

    /**
     *
     * @param string $namespace 
     */
    public function signOutCompte($namespace) {
        $this->signOutDeclaration();
        parent::signOutCompte($namespace);
    }

    /**
     * 
     */
    public function signOutTiers() {
        $this->signOutDeclaration();
        parent::signOutTiers();
    }
}
