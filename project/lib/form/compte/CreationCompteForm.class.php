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

        $this->_compte->email = $this->getValue('email');
        if ($this->getValue('mdp1')) {
            $this->_compte->setMotDePasseSSHA($this->getValue('mdp1'));
        }
        $this->_compte->save();

        return $this->_compte;
    }
}
