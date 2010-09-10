<?php

class ValidatorFirstConnection extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
       $this->addMessage('mdp_invalid', 'CVI ou code de création invalide.');
       $this->addMessage('compte_cree', 'Votre code a déja été créé.');
    }

    protected function doClean($values) {
        

        $cvi = isset($values['cvi']) ? $values['cvi'] : '';
        $mdp = isset($values['mdp']) ? $values['mdp'] : '';
        
        if($mdp && $cvi) {
            $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($cvi);
            if(substr($tiers->mot_de_passe, 0, 6)=='{SSHA}'){
                throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'compte_cree')));
            }elseif ($tiers && $tiers->mot_de_passe == '{TEXT}'.$mdp) {
                return array_merge($values, array('tiers' => $tiers));
            }else{
                throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'mdp_invalid')));
            }
        }

    }
}
