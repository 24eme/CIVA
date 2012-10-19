<?php

class ValidatorNewCvi extends sfValidatorInteger
{

  protected function configure($options = array(), $messages = array())
  {
    parent::configure();
    $this->addMessage('invalid_cvi', "Ce Cvi existe déja");
  }

  protected function doClean($value)
  {
      if (sfCouchdbManager::getClient('_Compte')->retrieveDocumentById('COMPTE-'.$value))
      {
      // throw new sfValidatorError($this, 'invalid_cvi', array('value' => $value));
      }
       parent::doClean($value);
  }

}