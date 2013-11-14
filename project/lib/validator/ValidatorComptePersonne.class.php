<?php

class ValidatorCompteLoginFirst extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->setMessage('unique', "Cettre adresse e-mail a déjà été ajouté.");
    }

    protected function doClean($values) {
        
        $comptes = _CompteClient::getInstance()->getComptesPersonnes();
        
        
        return $values;
    }

}
