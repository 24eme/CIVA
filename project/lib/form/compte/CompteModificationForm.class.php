<?php
class CompteModificationForm extends CompteForm {

    /**
     *
     */
    protected function checkCompte() {
        parent::checkCompte();
        if ($this->_compte->getStatutTeledeclarant() != CompteClient::STATUT_TELEDECLARANT_INSCRIT) {
            throw new sfException("compte must be status : INSCRIT");
        }
    }

    /**
     *
     */
    public function configure() {
        parent::configure();
        $this->getValidator('mdp1')->setOption('required', false);
        $this->getValidator('mdp2')->setOption('required', false);
    }

    /**
     *
     * @return _Compte
     */
    public function save() {
        if (!$this->isValid()) {
            throw new sfException("form must be valid");
        }

        $compte = CompteClient::getInstance()->find($this->_compte->_id);
        $compte->email = $this->getValue('email');
        if ($this->getValue('mdp1')) {
            $compte->setMotDePasseSSHA($this->getValue('mdp1'));
        }
        $compte->save();

        return $compte;
    }

}
