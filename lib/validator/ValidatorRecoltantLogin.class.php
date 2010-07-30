<?php

class ValidatorRecoltantLogin extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'Le numéro de cvi est incorrect.');
  }

  protected function doClean($values)
  {
    $cvi = isset($values['cvi']) ? $values['cvi'] : '';

    if ($cvi && $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($values['cvi']))
    {
       return array_merge($values, array('recoltant' => $recoltant));
    }

    throw new sfValidatorErrorSchema($this, array($this->getOption('cvi') => new sfValidatorError($this, 'invalid')));
  }
}
