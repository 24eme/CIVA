<?php

class myUser extends sfBasicSecurityUser {
    const SESSION_CVI = 'tiers_cvi';
    const NAMESPACE_TIERS = 'myUserTiers';

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

    public function signIn($tiers) {
        $this->setAttribute(self::SESSION_CVI, $tiers->getCvi(), self::NAMESPACE_TIERS);
        $this->setAuthenticated(true);
    }

    public function signInWithCas($casUser) {
        $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($casUser);
        $this->signIn($tiers);
    }

    public function signOut() {
        $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_TIERS);
        $this->_tiers = null;
        $this->setAuthenticated(false);
    }

    public function getTiers() {
        if (!$this->_tiers && $cvi = $this->getAttribute(self::SESSION_CVI, null, self::NAMESPACE_TIERS)) {
            $this->_tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($cvi); 

            if (!$this->_tiers) {
                // the user does not exist anymore in the database
                $this->signOut();
                throw new sfException('The user does not exist anymore in the database.');
            }
        }

        return $this->_tiers;
    }

    public function getDeclaration() {
        if (!isset($this->_declaration)) {
            $this->_declaration = $this->getTiers()->getDeclaration($this->getCampagne());
        }

        return $this->_declaration;
    }

    public function getCampagne() {
        return '2010';
    }

    public function getTiersCvi() {
        return $this->getTiers()->getCvi();
    }

}
