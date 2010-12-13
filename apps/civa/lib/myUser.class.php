<?php

class myUser extends sfBasicSecurityUser {
    const SESSION_CVI = 'tiers_cvi';
    const NAMESPACE_TIERS = 'myUserTiers';
    
    const ETAPE_EXPLOITATION = 'exploitation';
    const ETAPE_RECOLTE = 'recolte';
    const ETAPE_VALIDATION = 'validation';
    const CREDENTIAL_ADMIN = 'admin';
    const CREDENTIAL_DECLARANT = 'declarant';
    const CREDENTIAL_NON_DECLARANT = 'non_declarant';
    const CREDENTIAL_FICTIF = 'fictif';
    const CREDENTIAL_ETAPE_EXPLOITATION = 'etape_exploitation';
    const CREDENTIAL_ETAPE_RECOLTE = 'etape_recolte';
    const CREDENTIAL_ETAPE_VALIDATION = 'etape_validation';
    const CREDENTIAL_DECLARATION_BROUILLON = 'declaration_brouillon';
    const CREDENTIAL_DECLARATION_VALIDE = 'declaration_valide';

    protected $_etapes = array(self::ETAPE_EXPLOITATION, self::ETAPE_RECOLTE, self::ETAPE_VALIDATION);
    protected $_etapes_credential = array(self::ETAPE_EXPLOITATION => self::CREDENTIAL_ETAPE_EXPLOITATION, self::ETAPE_RECOLTE => self::CREDENTIAL_ETAPE_RECOLTE, self::ETAPE_VALIDATION => self::CREDENTIAL_ETAPE_VALIDATION);
    protected $_etapes_inclusion = array(self::ETAPE_EXPLOITATION => array(), self::ETAPE_RECOLTE => array(self::ETAPE_EXPLOITATION), self::ETAPE_VALIDATION => array(self::ETAPE_EXPLOITATION, self::ETAPE_RECOLTE));

    protected $_tiers = null;
    protected $_declaration = null;

    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            // remove user if timeout
            $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_TIERS);
            $this->user = null;
        }
    }

    public function signInAdmin() {
        $this->setAuthenticated(true);
        $this->addCredential(self::CREDENTIAL_ADMIN);
    }

    public function removeTiers() {
        $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_TIERS);
        $this->_tiers = null;
        $this->removeCredential(self::CREDENTIAL_DECLARANT);
    }

    public function signIn($tiers) {
        if (!$tiers)
            throw new sfCouchdbException('Tiers needed');

        $this->setAttribute(self::SESSION_CVI, $tiers->getCvi(), self::NAMESPACE_TIERS);
        $this->setAuthenticated(true);
        if ($tiers->isDeclarant()) {
            $this->addCredential(self::CREDENTIAL_DECLARANT);
        } else {
            $this->addCredential(self::CREDENTIAL_NON_DECLARANT);
        }
    }

    public function signInWithCas($casUser) {
       $ldap = new ldap();
       $groupe = $ldap->getGroupe($casUser);
       if($groupe == "declarant") {
           $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($casUser);
           $this->signIn($tiers);
       } elseif($groupe == "admin") {
           $this->signInAdmin();
       } elseif($groupe == "exterieur") {
           $tiers = $this->getTiersFictif($casUser);
           $this->signIn($tiers);
           $this->addCredential(self::CREDENTIAL_FICTIF);
       }
    }

    public function signOut() {
        $this->removeTiers();
        $this->setAuthenticated(false);
    }

    public function getTiers() {
        if (!$this->_tiers && $cvi = $this->getAttribute(self::SESSION_CVI, null, self::NAMESPACE_TIERS)) {
            if (!$this->isFictif()) {
                $this->_tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($cvi);
            } else {
                $this->_tiers = $this->getTiersFictif($cvi);
            }
            if (!$this->_tiers) {
                $this->signOut();
                throw new sfException('The user does not exist anymore in the database.');
            }
        }

        return $this->_tiers;
    }

    public function getDeclaration() {
        if (!isset($this->_declaration)) {
            try {
                $this->_declaration = $this->getTiers()->getDeclaration($this->getCampagne());
            } catch (Exception $exc) {
                $this->_declaration = null;
                $this->clearDeclarationCredentials();
            }
        }

        return $this->_declaration;
    }

    protected function addCredentialEtape($etape) {
        $this->addCredential($this->_etapes_credential[$etape]);
        foreach($this->_etapes_inclusion[$etape] as $item) {
            $this->addCredential($this->_etapes_credential[$item]);
        }
    }

    public function addEtape($etape) {
        if ($declaration = $this->getDeclaration()) {
           if (in_array($etape, $this->_etapes)) {
            $declaration->add('etape');
            if ($etape != $declaration->etape && (!array_key_exists($declaration->etape,  $this->_etapes_inclusion) || !in_array($etape, $this->_etapes_inclusion[$declaration->etape]))) {
                if ($this->verifyEtapeIsOk($etape)) {
                    $declaration->etape = $etape;
                    $declaration->save();
                    $this->addCredentialEtape($etape);
                }
            }
          }
        }
    }

    protected function verifyEtapeIsOk($etape) {
        if ($etape == self::ETAPE_EXPLOITATION) {
            return true;
        } elseif ($etape == self::ETAPE_RECOLTE) {
            return ($this->getDeclaration()->recolte->hasOneOrMoreAppellation());
        } elseif ($etape == self::ETAPE_VALIDATION) {
            return true;
        }
        return true;
    }

    public function clearDeclarationCredentials() {
        foreach($this->_etapes as $item) {
            $this->removeCredential($this->_etapes_credential[$item]);
        }
        $this->removeCredential(self::CREDENTIAL_DECLARATION_VALIDE);
        $this->removeCredential(self::CREDENTIAL_DECLARATION_BROUILLON);
    }
    
    public function initDeclarationCredentials() {
        $this->clearDeclarationCredentials();
        if(ConfigurationClient::getConfiguration()->exist('dr_non_editable'))
            $dr_non_editable = ConfigurationClient::getConfiguration()->dr_non_editable;
        else
            $dr_non_editable = 0;

        if ($dr_non_editable != 1) {
            $this->addCredential(self::CREDENTIAL_DECLARATION_BROUILLON);
            if ($declaration = $this->getDeclaration()) {
                if (!$this->isAdmin() && $declaration->isValideeTiers()) {
                    $this->removeCredential(self::CREDENTIAL_DECLARATION_BROUILLON);
                    $this->addCredential(self::CREDENTIAL_DECLARATION_VALIDE);
                } elseif($this->isAdmin() && $declaration->isValideeCiva()) {
                    $this->removeCredential(self::CREDENTIAL_DECLARATION_BROUILLON);
                    $this->addCredential(self::CREDENTIAL_DECLARATION_VALIDE);
                } else {
                    if ($declaration->exist('etape') && in_array($declaration->etape, $this->_etapes)) {
                        $this->addCredentialEtape($declaration->etape);
                    }
                }
            }
        }
    }

    public function getCampagne() {
        return '2010';
    }

    public function getTiersCvi() {
        return $this->getTiers()->getCvi();
    }

    public function isAdmin() {
        return ($this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_ADMIN));
    }

    public function isDeclarant() {
        return ($this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_DECLARANT));
    }

    public function isNonDeclarant() {
        return ($this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_NON_DECLARANT));
    }

    public function isFictif() {
        return ($this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_FICTIF));
    }

    protected function getTiersFictif($uid) {
        $ldap = new ldap();
        $tiers = $ldap->getTiersFromLdap($uid);
        $tiers->add('no_declarant', 1);
        $tiers->no_accises = '1';
        $tiers->setIsTiersFictif(true);
        return $tiers;
    }

}
