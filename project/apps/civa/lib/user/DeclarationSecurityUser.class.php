<?php

abstract class DeclarationSecurityUser extends TiersSecurityUser
{
    const CREDENTIAL_DECLARATION_EN_COURS = 'declaration_en_cours';
    const CREDENTIAL_DECLARATION_VALIDE = 'declaration_valide';
    const CREDENTIAL_ETAPE_EXPLOITATION = 'declaration_etape_exploitation';
    const CREDENTIAL_ETAPE_REPARTITION_RECOLTE = 'declaration_etape_repartition_recolte';
    const CREDENTIAL_ETAPE_RECOLTE = 'declaration_etape_recolte';
    const CREDENTIAL_ETAPE_VALIDATION = 'declaration_etape_validation';

    protected $_etapes_credentials = array(DR::ETAPE_EXPLOITATION => self::CREDENTIAL_ETAPE_EXPLOITATION,
                                           DR::ETAPE_REPARTITION => self::CREDENTIAL_ETAPE_REPARTITION_RECOLTE,
                                           DR::ETAPE_RECOLTE => self::CREDENTIAL_ETAPE_RECOLTE,
                                           DR::ETAPE_VALIDATION => self::CREDENTIAL_ETAPE_VALIDATION);
    protected $_credentials_declaration = array(
        self::CREDENTIAL_DECLARATION_EN_COURS,
        self::CREDENTIAL_DECLARATION_VALIDE,
        self::CREDENTIAL_ETAPE_EXPLOITATION,
        self::CREDENTIAL_ETAPE_REPARTITION_RECOLTE,
        self::CREDENTIAL_ETAPE_RECOLTE,
        self::CREDENTIAL_ETAPE_VALIDATION);
    protected $_declaration = null;
    protected $_ds = array();

    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options 
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
    {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            $this->signOutDeclaration();
        }
    }

    /**
     * 
     */
    protected function clearCredentialsDeclaration()
    {
        foreach ($this->_credentials_declaration as $credential) {
            $this->removeCredential($credential);
        }
    }

    /**
     * 
     */
    public function signOutDeclaration()
    {
        $this->_declaration = null;
        $this->_ds = array();
        $this->clearCredentialsDeclaration();
    }

    /**
     * @return DR
     */
    public function getDeclaration()
    {
        $this->requireDeclaration();
        $this->requireTiers();
        if (is_null($this->_declaration)) {
            $this->_declaration = $this->getDeclarant()->getDeclaration($this->getCampagne());
            if (!$this->_declaration) {
                $declaration = new DR();
                $declaration->set('_id', 'DR-' . $this->getDeclarant()->cvi . '-' . $this->getCampagne());
                return $declaration;
            }
        }

        return $this->_declaration;
    }

    /**
     * @return string
     */
    public function getCampagne()
    {
        return CurrentClient::getCurrent()->campagne;
    }

    /**
     * @return string
     */
    public function getCampagneDS()
    {
        return (int) (preg_replace("/^([0-9]{4})[0-9]{2}$/", '\1', $this->getPeriodeDS()) - 1);
    }

    public function getPeriodeDS(){
        if(CurrentClient::getCurrent()->isDSDecembre() && $this->getDeclarantDS() && $this->getDeclarantDS()->exist('ds_decembre')) {
            
            return CurrentClient::getCurrent()->getPeriodeDS();
        }
        
        if(CurrentClient::getCurrent()->isDSDecembre()) {

            return CurrentClient::getCurrent()->getAnneeDS()."07";
        }

        return CurrentClient::getCurrent()->getPeriodeDS();
    }

    /**
     * @return string
     */
    public function getMonthDS()
    {
        return substr($this->getPeriodeDS(), 4, 2);
    }
    
    /**
     * @return string
     */
    public function getAnneeDS()
    {
        return substr($this->getPeriodeDS(), 0, 4);
    }

    /**
     *
     * @param string $etape 
     */
    public function addEtapeDeclaration($etape)
    {
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
    protected function addCredentialsEtapeDeclaration()
    {
        $declaration = $this->getDeclaration();
        if ($declaration->exist('etape') && $declaration->etape) {
            $this->addCredential($this->_etapes_credentials[$declaration->etape]);
            foreach (DR::$_etapes_inclusion[$declaration->etape] as $etape) {
                $this->addCredential($this->_etapes_credentials[$etape]);
            }
        }
    }

    /**
     * returns trus if editable
     */
    public function isDrEditable()
    {
        if ($this->hasCredential(self::CREDENTIAL_OPERATEUR)) {
            return true;
        }
         
        return (CurrentClient::getCurrent()->dr_non_editable == 0 && CurrentClient::getCurrent()->dr_non_ouverte == 0);
    }
    
    /**
     * returns trus if validate
     */
    public function isDrValidee()
    {
        $declaration = $this->getDeclaration();
        
        if ($this->hasCredential(self::CREDENTIAL_ADMIN)) {
            return ($declaration->isValideeCiva());
        }

        return ($declaration->isValideeTiers() || $declaration->isValideeCiva()); 
    }

    /**
     * 
     */
    public function initCredentialsDeclaration()
    {
        $this->requireDeclaration();
        $declaration = $this->getDeclaration();
        $this->clearCredentialsDeclaration();
        if ($this->isDrEditable()) {
            if ($this->isDrValidee()) {
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
    protected function requireDeclaration()
    {
        $this->requireTiers();
        if (!$this->hasCredential(self::CREDENTIAL_DECLARATION)) {
            throw new sfException("you must be logged in with a tiers");
        }
    }
    
    
    /**
     * DS
     */

    public function isDeclarantDSDecembre() {

        return $this->getDeclarantDS() && $this->getDeclarantDS()->exist('ds_decembre') && $this->getDeclarantDS()->ds_decembre;
    }

    public function isDsEditable()
    {
        if ($this->hasCredential(self::CREDENTIAL_OPERATEUR)) {
            return true;
        }

        return (!$this->isDsTerminee() || !$this->isDsNonOuverte());
    }

    public function isDsTerminee()
    {

        if(CurrentClient::getCurrent()->isDSDecembre() && !$this->isDeclarantDSDecembre()) {

            return true;
        }

        return CurrentClient::getCurrent()->ds_non_editable == 1;
    }

    public function isDsNonOuverte()
    {
        if(CurrentClient::getCurrent()->isDSDecembre() && !$this->isDeclarantDSDecembre()) {

            return true;
        }

        return CurrentClient::getCurrent()->ds_non_ouverte == 1;
    }
    
    public function getDs($type_ds)
    {
        $declarant = $this->getDeclarantDS($type_ds);
        if(!$declarant->isDeclarantStock()) {
            throw new sfException("Vous n'avez pas les droits pour créez une DS");
        }

        if (!$declarant->hasLieuxStockage() && !$declarant->isAjoutLieuxDeStockage()) {                                                                                                                                                
            return null;
        }

        $this->requireTiers();
        if (!isset($this->_ds[$type_ds])) {
            $periode = $this->getPeriodeDS();
            $this->_ds[$type_ds] = $declarant->getDs($periode);
            if (!isset($this->_ds[$type_ds])) {
                $ds = new DSCiva();
                if($declarant->exist('civaba') && $declarant->civaba){
                    $ds->add('civaba', $declarant->civaba);
                }
                $ds->add('type_ds', $type_ds);
                $ds->identifiant = $declarant->getIdentifiant();
                $ds->set('_id', 'DS-' . $declarant->getIdentifiant() . '-' .$periode.'-'.$declarant->getLieuStockagePrincipal(true)->getNumeroIncremental());
                return $ds;
            }
        }

        return $this->_ds[$type_ds];
    }

    public function removeDs($type_ds = null)
    {
        $dss = DSCivaClient::getInstance()->findDssByDS($this->getDs($type_ds));
        foreach ($dss as $ds) {
            $ds->delete();
        }
        $this->signOutDeclaration();
    }
    

    /**
     *
     * @param _Tiers $tiers 
     */
    public function signInTiers($tiers)
    {
        parent::signInTiers($tiers);
        if ($this->hasCredential(myUser::CREDENTIAL_DECLARATION)) {
            $this->initCredentialsDeclaration();
        }
    }

    /**
     *
     * @param string $namespace 
     */
    public function signOutCompte($namespace = self::NAMESPACE_COMPTE_USED)
    {
        $this->signOutDeclaration();
        parent::signOutCompte($namespace);
    }

    /**
     * 
     */
    public function signOutTiers()
    {
        $this->signOutDeclaration();
        parent::signOutTiers();
    }

    public function removeDeclaration()
    {
        $this->getDeclaration()->delete();
        $this->signOutDeclaration();
        $this->initCredentialsDeclaration();
    }

}
