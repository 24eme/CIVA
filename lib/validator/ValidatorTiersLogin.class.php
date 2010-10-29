<?php
    class ValidatorTiersLogin extends sfValidatorBase
    {
      public function configure($options = array(), $messages = array())
      {
        $this->addOption('create_required', true);
        $this->setMessage('invalid', 'Le numéro de CVI est incorrect.');
        $this->addMessage('not_create', "Vous n'avez pas encore créé votre compte. <br /> <br /> Pour ce faire munissez-vous de votre code d'accès reçu par courrier et cliquez sur le lien créer votre compte.");
      }

      protected function doClean($values)
      {
        $cvi = isset($values['cvi']) ? $values['cvi'] : '';

        if ($cvi && $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($values['cvi']))
        {
           if ($this->getOption('create_required') === true && substr($tiers->mot_de_passe, 0, 6)=='{TEXT}') {
               throw new sfValidatorErrorSchema($this, array($this->getOption('cvi') => new sfValidatorError($this, 'not_create')));
           } else {
               return array_merge($values, array('tiers' => $tiers));
           }
        }

        throw new sfValidatorErrorSchema($this, array($this->getOption('cvi') => new sfValidatorError($this, 'invalid')));
      }
    }
