<?php
class CompteModificationOublieForm extends CompteForm {
    protected function checkCompte() {
        parent::checkCompte();
        if ($this->_compte->getStatus() != _Compte::STATUS_MOT_DE_PASSE_OUBLIE) {
            throw new sfException("compte must be status : OUBLIE");
        }
    }

    public function configure() {
        parent::configure();
        unset($this['email']);
    }

    public function save() {
        if ($this->isValid()) {
            $this->_compte->setMotDePasseSSHA($this->getValue('mdp1'));
            $this->_compte->save();
        } else {
            throw new sfException("form must be valid");
        }
        return $this->_compte;
    }
}
