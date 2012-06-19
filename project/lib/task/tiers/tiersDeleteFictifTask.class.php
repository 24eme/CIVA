<?php

class tiersDeleteFictifTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
       new sfCommandArgument('uid', sfCommandArgument::REQUIRED, 'Identifiant'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'tiers';
    $this->name             = 'delete-fictif';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiersDeleteFictif|INFO] task does things.
Call it with:

  [php symfony tiersDeleteFictif|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $tiers = new Tiers();
    $tiers->cvi = $arguments['uid'];

    $ldap = new ldap();
    if ($ldap->ldapVerifieExistence($tiers)) {
        if ($ldap->getGroupe($tiers->cvi) == 'exterieur') {
            if ($ldap->ldapDelete($tiers)) {
                $this->logSection("deleted", $tiers->cvi);
            } else {
                throw new sfCommandException("L'utilisateur n'a pas pu être supprimer");
            }
        } else {
            throw new sfCommandException("L'utilisateur n'appartient pas au groupe \"extérieur\"");
        }
    } else {
        throw new sfCommandException("Cet utilisateur n'existe pas");
    }

    // add your code here
  }
}
