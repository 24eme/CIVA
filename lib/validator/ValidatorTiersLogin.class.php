<?php
    class ValidatorTiersLogin extends sfValidatorBase
    {
      public function configure($options = array(), $messages = array())
      {
        $this->setMessage('invalid', 'Le numéro de CVI est incorrect.');
      }

      protected function doClean($values)
      {
        $cvi = isset($values['cvi']) ? $values['cvi'] : '';

        if ($cvi && $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($values['cvi']))
        {
           return array_merge($values, array('tiers' => $tiers));
        }

        throw new sfValidatorErrorSchema($this, array($this->getOption('cvi') => new sfValidatorError($this, 'invalid')));
      }
    }
