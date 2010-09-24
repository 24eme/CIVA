<?php

class ValidatorTiersLoginAdmin extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'Le numéro de CVI est incorrect.');
    $this->addMessage('invalid_login', 'Mot de passe et / ou login incorrect');
    $this->addRequiredOption('need_login');
  }

  protected function doClean($values)
  {
    $need_login = $this->getOption('need_login', true);
    $is_login = false;
    $configuration = ConfigurationClient::getConfiguration();
    if ($need_login) {
        $username =  isset($values['username']) ? $values['username'] : '';
        $password =  isset($values['password']) ? $values['password'] : '';
        $is_login = $configuration->isCompteAdminExist($username, $password);
    } else {
        $is_login = true;
    }
    
    $cvi = isset($values['cvi']) ? $values['cvi'] : '';

    if ($is_login) {
        if ($cvi && $tiers = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($values['cvi']))
        {
            return array_merge($values, array('tiers' => $tiers));
        } else {
            throw new sfValidatorErrorSchema($this, array($this->getOption('cvi') => new sfValidatorError($this, 'invalid')));
        }
    } else {
        throw new sfValidatorErrorSchema($this, array($this->getOption('username') => new sfValidatorError($this, 'invalid_login')));
    }
  }
}
