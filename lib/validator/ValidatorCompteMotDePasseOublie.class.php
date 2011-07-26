<?php

class ValidatorCompteMotDePasseOublie extends sfValidatorBase {

    public function configure($options = array(), $messages = array()) {
        $this->setMessage('invalid', 'Le numéro de CVI est incorrect.');
        $this->addMessage('invalid_status', "Vous n'avez pas encore créé votre compte. <br /> <br /> Pour ce faire munissez-vous de votre code d'accès reçu par courrier et cliquez sur le lien créer votre compte.");
    }

    protected function doClean($values) {
        if(!$values['login']) {
            return array_merge($values);
        }
        
        $compte = sfCouchdbManager::getClient('_Compte')->retrieveByLogin($values['login']);

        if (!$compte) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'invalid')));
        }

        if (in_array($compte->getStatus(), array(_Compte::STATUS_INSCRIT, _Compte::STATUS_MOT_DE_PASSE_OUBLIE))) {
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'invalid_status')));
        }

        return array_merge($values, array('compte' => $compte));
    }

}

