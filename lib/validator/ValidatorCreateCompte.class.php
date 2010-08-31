<?php

class ValidatorCreateCompte extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
        $this->addMessage('mdp_invalid', 'Vos deux mots de passe ne correspondent pas.');
    }

    protected function doClean($values) {
        if($values['mdp1'] == $values['mdp2'] && $values['mdp1'] != '') {
            $recoltant = sfContext::getInstance()->getUser()->getRecoltant();
            $recoltant->mot_de_passe = $recoltant->make_ssha_password($values['mdp1']);
            $recoltant->email = $values['email'];
            $recoltant->save();

            return array_merge($values, array('recoltant' => $recoltant));

        }
        throw new sfValidatorErrorSchema($this, array($this->getOption('mdp1') => new sfValidatorError($this, 'mdp_invalid')));
    }
}
