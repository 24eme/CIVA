<?php

class compteCreateVirtuelTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
        new sfCommandArgument('login', sfCommandArgument::REQUIRED, 'Login'),
        new sfCommandArgument('pass', sfCommandArgument::REQUIRED, 'Mot de passe'),
        new sfCommandArgument('nom', sfCommandArgument::REQUIRED, 'Nom'),
        new sfCommandArgument('email', sfCommandArgument::REQUIRED, 'Email'),
        new sfCommandArgument('commune', sfCommandArgument::REQUIRED, 'Commune'),
        new sfCommandArgument('code_postal', sfCommandArgument::REQUIRED, 'Code Postal'),
        new sfCommandArgument('droits', sfCommandArgument::IS_ARRAY, 'Droits'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('suppression', null, sfCommandOption::PARAMETER_REQUIRED, 'Supprime si existant', false),
    ));

    $this->namespace        = 'compte';
    $this->name             = 'create-virtuel';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [tiersCreateFictif|INFO] task does things.
Call it with:

  [php symfony tiersCreateFictif|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if ($compte = acCouchdbManager::getClient()->find('COMPTE-'.$arguments['login'])) {
        if ($options['suppression']) {
          $compte->delete();
        } else {
          throw new sfCommandException(sprintf("Le compte \"%s\" existe déjà", $arguments['login']));
        }
    }
    
    $compte = new CompteVirtuel();
    $compte->set('_id', 'COMPTE-'.$arguments['login']);
    $compte->login = $arguments['login'];
    if (substr($arguments['pass'], 0, 6) == "{SSHA}") {
      $compte->mot_de_passe = $arguments['pass'];
    } else {
      $compte->setPasswordSSHA($arguments['pass']);
    }
    $compte->email = $arguments['email'];
    $compte->nom = $arguments['nom'];
    $compte->commune = $arguments['commune'];
    $compte->code_postal = $arguments['code_postal'];
    $compte->droits = $arguments['droits'];
    $compte->save();
    
    $this->logSection("created", $compte->login);
  }
}
