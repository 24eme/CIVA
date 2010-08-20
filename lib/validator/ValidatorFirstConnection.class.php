<?php

class ValidatorFirstConnection extends sfValidatorBase {
    public function configure($options = array(), $messages = array()) {
       $this->addMessage('mdp_invalid', 'CVI ou mot de passe invalide.');
    }

    protected function doClean($values) {
        

        $cvi = isset($values['cvi']) ? $values['cvi'] : '';
        $mdp = isset($values['mdp']) ? $values['mdp'] : '';
        
        if($mdp && $cvi) {
            $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($values['cvi']);
            /*$recoltant->mdp = md5('0000');
            $recoltant->change_mdp = 0;
            $recoltant->save();
            exit();*/
            if ($recoltant && $recoltant->mdp == md5($mdp)) {
                return array_merge($values, array('recoltant' => $recoltant));
            }
            
            throw new sfValidatorErrorSchema($this, array($this->getOption('mdp') => new sfValidatorError($this, 'mdp_invalid')));
        }

    }
}
