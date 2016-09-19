<?php
class CreationCompteForm extends CompteForm {

    /**
     *
     */
    protected function checkCompte() {
        parent::checkCompte();
        if ($this->_compte->getStatus() != CompteClient::STATUT_TELEDECLARANT_NOUVEAU) {
            throw new sfException("compte must be status : NOUVEAU");
        }
    }

    /**
     *
     */
    public function configure() {
        parent::configure();
    }

    /**
     *
     * @return _Compte
     */
    public function save() {
        if (!$this->isValid()) {
            throw new sfException("form must be valid");
        }

        $master = $this->_compte->getMasterObject();
        $master->setEmail($this->getValue('email'));
        $master->save();

        $compte = CompteClient::getInstance()->find($this->_compte->_id);
        if ($this->getValue('mdp1')) {
            $compte->setMotDePasseSSHA($this->getValue('mdp1'));
        }
        $compte->save();

        return $this->_compte;
    }
}
