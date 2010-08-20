<?php

class ValidatorCreateCompte extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
        $this->addMessage('mdp_invalid', 'Vos deux mots de passe ne correspondent pas.');
    }

    protected function doClean($values) {
        echo $values['mdp1'].' '.$values['mdp2'];
        if($values['mdp1'] == $values['mdp2']) {
            $recoltant = sfContext::getInstance()->getUser()->getRecoltant();
            $recoltant->mdp = md5($values['mdp1']);
            $recoltant->email = $values['email'];
            $recoltant->change_mdp = '1';
            $recoltant->save();

            return array_merge($values, array('recoltant' => $recoltant));

        }
        throw new sfValidatorErrorSchema($this, array($this->getOption('mdp1') => new sfValidatorError($this, 'mdp_invalid')));
    }
}
