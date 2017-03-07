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

        $this->_compte->email = $this->getValue('email');
        if ($this->getValue('mdp1')) {
            $this->_compte->setMotDePasseSSHA($this->getValue('mdp1'));
        }
        $this->_compte->save();

        return $this->_compte;
    }

}
