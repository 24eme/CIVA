<?php
class CompteModificationForm extends CompteForm {
    
    /**
     * 
     */
    protected function checkCompte() {
        parent::checkCompte();
        if ($this->_compte->getStatus() != _Compte::STATUS_INSCRIT) {
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
        if ($this->isValid()) {
            if ($this->getValue('mdp1')) {
                $this->_compte->setPasswordSSHA($this->getValue('mdp1'));
            }
            $this->_compte->email = $this->getValue('email');
            $this->_compte->save();
            $this->_compte->updateLdap();
        } else {
            throw new sfException("form must be valid");
        }
        
        return $this->_compte;
    }
    
}
