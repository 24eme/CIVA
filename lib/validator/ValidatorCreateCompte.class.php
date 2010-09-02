<?php

class ValidatorCreateCompte extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
        $this->addMessage('mdp_invalid', 'Vos deux mots de passe ne correspondent pas.');
    }

    protected function doClean($values) {
        if($values['mdp1'] == $values['mdp2']) {
            $tiers = sfContext::getInstance()->getUser()->getTiers();
            if($values['mdp1']!= '' ) $tiers->mot_de_passe = $tiers->make_ssha_password($values['mdp1']);
            $tiers->email = $values['email'];
            $tiers->save();

            return array_merge($values, array('tiers' => $tiers));

        }
        throw new sfValidatorErrorSchema($this, array($this->getOption('mdp1') => new sfValidatorError($this, 'mdp_invalid')));
    }
}
