<?php

class ValidatorNewCvi extends sfValidatorInteger
{

  protected function configure($options = array(), $messages = array())
  {
	    parent::configure();
	    $this->setMessage("required", "Champs obligatoire");
		$this->addMessage('cvi_exist', "Ce Cvi existe déja");
	    $this->setMessage('max', "Le cvi doit être composé de 10 digits");
      $this->setMessage('min', "Le cvi doit être composé de 10 digits");
      $this->setMessage('invalid', "Le cvi doit être composé de 10 digits");
  }

  protected function doClean($value)
  {
  	
      if (acCouchdbManager::getClient('_Compte')->find('COMPTE-'.$value))
      {
      	  throw new sfValidatorError($this, 'cvi_exist', array('value' => $value));
      }
       parent::doClean($value);
  }

}