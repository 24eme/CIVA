<?php
class CreationCompteForm extends CompteForm {
    
    /**
     * 
     */
    protected function checkCompte() {
        parent::checkCompte();
        if ($this->_compte->getStatus() != _Compte::STATUS_NOUVEAU) {
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
        if ($this->isValid()) {
            $this->_compte->email = $this->getValue('email');
            $this->_compte->setPasswordSSHA($this->getValue('mdp1'));
            $this->_compte->save();
            $this->_compte->updateLdap();
        } else {
            throw new sfException("form must be valid");
        }
        
        return $this->_compte;
    }
}
