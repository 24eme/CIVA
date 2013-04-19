<?php

class ValidatorCompteLoginFirst extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->setMessage('invalid', 'CVI ou code de création invalide.');
        $this->addMessage('invalid_status', 'Votre compte a déja été créé.');
    }

    protected function doClean($values) {
        if (!$values['login'] || !$values['mdp']) {
            return array_merge($values);
        }
        
        $compte = acCouchdbManager::getClient('_Compte')->retrieveByLogin($values['login']);

        if (!$compte) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'invalid')));
        }
                
        if ($compte->getStatus() != _Compte::STATUS_NOUVEAU){
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'invalid_status')));
        }
        
        if ($compte->mot_de_passe != '{TEXT}' . $values['mdp']) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'invalid')));
        }
            
        return array_merge($values, array('compte' => $compte));
    }

}
