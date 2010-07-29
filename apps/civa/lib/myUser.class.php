<?php

class myUser extends sfBasicSecurityUser {
    const SESSION_CVI = 'recoltant_cvi';
    const NAMESPACE_RECOLTANT = 'myUserRecoltant';

    protected $_recoltant = null;
    protected $_declaration = null;

    public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array()) {
        parent::initialize($dispatcher, $storage, $options);

        if (!$this->isAuthenticated()) {
            // remove user if timeout
            $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_RECOLTANT);
            $this->user = null;
        }
    }

    public function signIn($recoltant) {
        $this->setAttribute(self::SESSION_CVI, $recoltant->getCvi(), self::NAMESPACE_RECOLTANT);
        $this->setAuthenticated(true);
    }

    public function signOut() {
        $this->getAttributeHolder()->removeNamespace(self::NAMESPACE_RECOLTANT);
        $this->_recoltant = null;
        $this->setAuthenticated(false);
    }

    public function getRecoltant() {
        if (!$this->_recoltant && $cvi = $this->getAttribute(self::SESSION_CVI, null, self::NAMESPACE_RECOLTANT)) {
            $this->_recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($cvi); 

            if (!$this->_recoltant) {
                // the user does not exist anymore in the database
                $this->signOut();
                throw new sfException('The user does not exist anymore in the database.');
            }
        }

        return $this->_recoltant;
    }

    public function getDeclaration() {
        if (!isset($this->_declaration)) {
            $this->_declaration = $this->getRecoltant()->getDeclaration($this->getCampagne());
        }

        return $this->_declaration;
    }

    public function getCampagne() {
        return '2010';
    }

    public function getRecoltantCvi() {
        return $this->getRecoltant()->getCvi();
    }

}
