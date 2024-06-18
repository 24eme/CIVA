<?php

abstract class CompteSecurityUser extends sfBasicSecurityUser {

    protected $_compte = array();

    const SESSION_COMPTE = 'compte';
    const NAMESPACE_COMPTE_AUTHENTICATED = "CompteSecurityUser_Authenticated";
    const NAMESPACE_COMPTE_USED = "CompteSecurityUser_Used";
    const CREDENTIAL_COMPTE = 'compte';
    const CREDENTIAL_COMPTE_TIERS= 'compte_tiers';
    const CREDENTIAL_ADMIN = _CompteClient::DROIT_ADMIN;
    const CREDENTIAL_OPERATEUR = _CompteClient::DROIT_OPERATEUR;
    const CREDENTIAL_DELEGATION = 'delegation';

    protected $_namespaces_compte = array(self::NAMESPACE_COMPTE_AUTHENTICATED,
                                          self::NAMESPACE_COMPTE_USED);

    protected $_credentials_compte = array(self::CREDENTIAL_OPERATEUR,
                                           self::CREDENTIAL_ADMIN,
                                           self::CREDENTIAL_DELEGATION,
                                           self::CREDENTIAL_COMPTE_TIERS);

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
        $cas_user =  preg_replace('/^[c]{1}([0-9]{10})$/', 'C\1', $cas_user);

        $compte = CompteClient::getInstance()->findByLogin($cas_user);
        if (!$compte) {
            throw new sfException('compte does not exist');
        }
        $this->signInFirst($compte);
    }

    /**
     *
     * @param _Compte $compte
     */
    public function signInFirst($compte) {
        $this->addCredential(self::CREDENTIAL_COMPTE);
        $this->addCredential("drm");
        $this->signInCompte($compte, self::NAMESPACE_COMPTE_AUTHENTICATED);
        $this->signInCompte($compte, self::NAMESPACE_COMPTE_USED);

        $this->setAuthenticated(true);
        $this->updateCredentialsCompte();
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

    protected function clearCredentialsCompte() {
        foreach($this->_credentials_compte as $credential) {
                $this->removeCredential($credential);
        }
    }

    protected function initCredentialsCompte() {
        foreach($this->_namespaces_compte as $namespace) {
            $compte = $this->getCompte($namespace);
            foreach ($compte->add('droits') as $credential) {
                $this->addCredential($credential);
            }
            if($this->isAdmin()) {
                $this->addCredential(_CompteClient::DROIT_OPERATEUR);
            }
            if($compte->exist('delegation') && $this->getCompte()->login == $this->getCompte(self::NAMESPACE_COMPTE_AUTHENTICATED)->login){
                $this->addCredential(self::CREDENTIAL_DELEGATION);
            }

            if ($compte->type == 'Compte' && $compte->getSociete() && count($compte->getSociete()->etablissements) && !$this->isAdmin() && !$this->isSimpleOperateur()) {
                $this->addCredential(self::CREDENTIAL_COMPTE_TIERS);
            }
        }
    }

    protected function updateCredentialsCompte() {
            $this->clearCredentialsCompte();
              $this->initCredentialsCompte();
    }

    public function signInCompteUsed($compte) {
        $this->signInCompte($compte, self::NAMESPACE_COMPTE_USED);
        $this->updateCredentialsCompte();
    }

    /**
     *
     * @param _Compte $compte
     */
    protected function signInCompte($compte, $namespace) {

        if ($compte->type == 'CompteProxy') {
            return $this->signInCompte(acCouchdbManager::getClient()->find($compte->compte_reference), $namespace);
        }

        $this->signOutCompte($namespace);
        $this->setAttribute(self::SESSION_COMPTE, $compte->login, $namespace);

    }

    public function signOutCompteUsed() {
        $this->signOutCompte(self::NAMESPACE_COMPTE_USED);
        $this->signInCompteUsed($this->getCompte(self::NAMESPACE_COMPTE_AUTHENTICATED));
    }

    /**
     *
     * @param string $namespace
     */
    protected function signOutCompte($namespace) {
        $this->_compte = array();
        $this->getAttributeHolder()->removeNamespace($namespace);
    }

    /**
     *
     * @return _Compte $compte
     */
    public function getCompte($namespace = self::NAMESPACE_COMPTE_USED) {
        $this->requireCompte();

        if (!array_key_exists($namespace, $this->_compte)) {
            $this->_compte[$namespace] = CompteClient::getInstance()->findByLogin($this->getAttribute(self::SESSION_COMPTE, null, $namespace));
            if (!$this->_compte[$namespace]) {
                $this->signOut();
                throw new sfException("The compte does not exist");
            }
        }

        return $this->_compte[$namespace];
    }

    public function isSimpleOperateur() {

        return $this->hasCredential(self::CREDENTIAL_OPERATEUR) && !$this->hasCredential(self::CREDENTIAL_ADMIN);
    }

    public function isAdmin()
    {
        return $this->hasCredential(self::CREDENTIAL_ADMIN);
    }

    protected function requireCompte() {
        if (!$this->isAuthenticated() && $this->hasCredential(self::CREDENTIAL_COMPTE)) {
	        throw new sfException("you must be logged with a tiers");
        }
    }

    public function isInDelegateMode() {
        return ($this->getAttribute(self::SESSION_COMPTE, null, self::NAMESPACE_COMPTE_AUTHENTICATED) != $this->getAttribute(self::SESSION_COMPTE, null, self::NAMESPACE_COMPTE_USED));
    }

}
