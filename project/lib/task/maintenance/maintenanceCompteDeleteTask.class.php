<?php

class maintenanceCompteDeleteTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'compte-delete';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '512M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if (!$this->askConfirmation('Voulez vous supprimer les Comptes ?', 'QUESTION', false)) {
        exit;
    }

    $compte_ids = acCouchdbManager::getClient("_Compte")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

    foreach ($compte_ids as $id) {
        $compte_json = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
        acCouchdbManager::getClient()->deleteDoc($compte_json);
        $this->logSection("delete", $id);
    }
  }
}
