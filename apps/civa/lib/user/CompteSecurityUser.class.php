<?php

abstract class CompteSecurityUser extends sfBasicSecurityUser {

    protected $_compte = array();
    
    const SESSION_COMPTE = 'compte';
    const NAMESPACE_COMPTE_AUTHENTICATED = "CompteSecurityUser_Authenticated";
    const NAMESPACE_COMPTE_TIERS = "CompteSecurityUser_Tiers";
    const NAMESPACE_COMPTE_PROXY = "CompteSecurityUser_Proxy";
    const NAMESPACE_COMPTE_VIRTUEL = "CompteSecurityUser_Virtuel";
    
    const CREDENTIAL_COMPTE = 'compte';
    const CREDENTIAL_COMPTE_TIERS = 'compte_tiers';
    const CREDENTIAL_COMPTE_PROXY = 'compte_proxy';
    const CREDENTIAL_COMPTE_VIRTUEL = 'compte_virtuel';
    const CREDENTIAL_OPERATEUR = 'operateur';
    const CREDENTIAL_ADMIN = 'admin';

    protected $_couchdb_type_namespace_compte= array("CompteTiers" => self::NAMESPACE_COMPTE_TIERS, 
                                                     "CompteProxy" => self::NAMESPACE_COMPTE_PROXY, 
                                                     "CompteVirtuel" => self::NAMESPACE_COMPTE_VIRTUEL);
    
    protected $_namespace_credential_compte = array(self::NAMESPACE_COMPTE_TIERS => self::CREDENTIAL_COMPTE_TIERS, 
                                                   self::NAMESPACE_COMPTE_PROXY => self::CREDENTIAL_COMPTE_PROXY,
                                                   self::NAMESPACE_COMPTE_VIRTUEL => self::CREDENTIAL_COMPTE_VIRTUEL);
    
    protected $_namespaces_compte = array(self::NAMESPACE_COMPTE_AUTHENTICATED,
                                          self::NAMESPACE_COMPTE_TIERS, 
                                          self::NAMESPACE_COMPTE_PROXY, 
                                          self::NAMESPACE_COMPTE_VIRTUEL);
    
    protected $_credentials_compte = array(self::CREDENTIAL_COMPTE, 
                                           self::CREDENTIAL_COMPTE_TIERS, 
                                           self::CREDENTIAL_COMPTE_PROXY, 
                                           self::CREDENTIAL_COMPTE_VIRTUEL,
                                           self::CREDENTIAL_OPERATEUR,
                                           self::CREDENTIAL_ADMIN);

    /**
     *
     * @param sfEventDispatcher $dispatcher
     * @param sfStorage $storage
     * @param type $options 
     */
    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            $this->signOut();
        }
    }

    /**
     *
     * @param string $cas_user 
     */
    public function signIn($cas_user) {
        $compte = sfCouchdbManager::getClient('_Compte')->retrieveByLogin($cas_user);
        if (!$compte) {
            throw new sfException('compte does not exist');
        }
        $this->addCredential(self::CREDENTIAL_COMPTE);
        $this->signInCompteAutenticated($compte);
        $this->signInCompte($compte);
        
        $this->setAuthenticated(true);
        
        if ($compte->getStatus() == _Compte::STATUS_MOT_DE_PASSE_OUBLIE) {
            $compte->resetMotDePasseFromLdap();
            $compte->save();
        }
    }
    
    /**
     *
     * @param _Compte $compte 
     */
    public function signInFirst($compte) {
        $this->addCredential(self::CREDENTIAL_COMPTE);
        $this->signInCompte($compte);
        $this->setAuthenticated(true);
    }

    /**
     * 
     */
    public function signOut() {
        foreach($this->_namespaces_compte as $namespace) {
            $this->signOutCompte($namespace);
        }
        $this->setAuthenticated(false);
        $this->clearCredentials();
    }
    
    /**
     *
     * @param _Compte $compte 
     */
    public function signInCompteAutenticated($compte) {
        $this->setAttribute(self::SESSION_COMPTE, $compte->login, self::NAMESPACE_COMPTE_AUTHENTICATED);
    }

    /**
     *
     * @param _Compte $compte 
     */
    public function signInCompte($compte) {
        $namespace = $this->_couchdb_type_namespace_compte[$compte->type];
        $this->signOutCompte($namespace);
        $this->setAttribute(self::SESSION_COMPTE, $compte->login, $namespace);
        $this->addCredential($this->_namespace_credential_compte[$namespace]);
        foreach ($compte->droits as $credential) {
            //if (in_array($credential, $this->_credentials_compte)) {
                $this->addCredential($credential);
            //}
        }
        if ($compte->type == 'CompteProxy') {
            $this->signInCompte(sfCouchdbManager::getClient()->retrieveDocumentById($compte->compte_reference));
        }
    }
    
    /**
     *
     * @param string $namespace 
     */
    public function signOutCompte($namespace) {
        $this->_compte = array();
        $this->removeCredential($this->_namespace_credential_compte[$namespace]);
        $this->getAttributeHolder()->removeNamespace($namespace);
    }

    /**
     *
     * @return _Compte $compte 
     */
    public function getCompte($namespace = null) {
        $this->requireCompte();
        
        if (!$namespace) {
            $namespace = $this->getNamespaceCompte();
        }
        
        if (!array_key_exists($namespace, $this->_compte)) {
            $this->_compte[$namespace] = sfCouchdbManager::getClient('_Compte')->retrieveByLogin($this->getAttribute(self::SESSION_COMPTE, null, $namespace));
            if (!$this->_compte[$namespace]) {
                $this->signOut();
                throw new sfException("The compte does not exist");
            }
        }

        return $this->_compte[$namespace];
    }
    
    protected function getNamespaceCompte() {
        if($this->hasCredential(self::CREDENTIAL_COMPTE_PROXY)) {
            return self::NAMESPACE_COMPTE_PROXY;
        } elseif ($this->hasCredential(self::CREDENTIAL_COMPTE_TIERS)) {
            return self::NAMESPACE_COMPTE_TIERS;
        } elseif($this->hasCredential(self::CREDENTIAL_COMPTE_VIRTUEL)) {
            return self::NAMESPACE_COMPTE_VIRTUEL;
        } else {
            throw new sfException("no namespace existing");
        }
    }

    /**
     * 
     */
    protected function requireCompte() {
        if (!$this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_COMPTE)) {
	  throw new sfException("you must be logged with a tiers");
        }
    }

}
