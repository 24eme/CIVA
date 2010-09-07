<?php

class ValidatorFirstConnection extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
       $this->addMessage('mdp_invalid', 'CVI ou code de création invalide.');
    }

    protected function doClean($values) {
        

        $cvi = isset($values['cvi']) ? $values['cvi'] : '';
        $mdp = isset($values['mdp']) ? $values['mdp'] : '';
        
        if($mdp && $cvi) {
            $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($cvi);
            if ($tiers && $tiers->mot_de_passe == '{TEXT}'.$mdp) {
                return array_merge($values, array('tiers' => $tiers));
            }
            
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'mdp_invalid')));
        }

    }
}
