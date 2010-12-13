<?php

class tiersCreateFictifTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
        new sfCommandArgument('uid', sfCommandArgument::REQUIRED, 'Identifiant'),
        new sfCommandArgument('pass', sfCommandArgument::REQUIRED, 'Mot de passe'),
        new sfCommandArgument('nom', sfCommandArgument::REQUIRED, 'Nom'),
        new sfCommandArgument('email', sfCommandArgument::REQUIRED, 'Email'),
        new sfCommandArgument('commune', sfCommandArgument::REQUIRED, 'Commune'),
        new sfCommandArgument('code_postal', sfCommandArgument::REQUIRED, 'Code Postal'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'tiers';
    $this->name             = 'create-fictif';
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

    if (substr($arguments['uid'], 0, 4) != 'ext-') {
        throw new sfCommandException("L'identifiant doit commencer par \"ext-\"");
    }

    $tiers = new Tiers();
    $tiers->cvi = $arguments['uid'];
    $tiers->mot_de_passe = $tiers->make_ssha_password($arguments['pass']);
    $tiers->nom = $arguments['nom'];
    $tiers->email = $arguments['email'];
    $tiers->setCommune($arguments['commune']);
    $tiers->setCodePostal($arguments['code_postal']);

    $ldap = new ldap();
    if (!$ldap->ldapVerifieExistence($tiers)) {
        if ($ldap->ldapAdd($tiers, 'exterieur')) {
            $this->logSection("created", $tiers->cvi);
        } else {
            throw new sfCommandException("Une erreur est survenue lors de la création");
        }   
    } else {
        throw new sfCommandException(sprintf("Le compte \"%s\" existe déjà", $tiers->cvi));
    }
    // add your code here
  }
}
