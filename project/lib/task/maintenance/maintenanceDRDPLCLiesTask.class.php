<?php

class maintenanceDRDPLCLiesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
     $this->addArguments(array(
       new sfCommandArgument('id', sfCommandArgument::REQUIRED, 'ID du doc'),
     ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      new sfCommandOption('delete', null, sfCommandOption::PARAMETER_REQUIRED, 'Delete', false),
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'Campagne', '2009'),
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-dplc-lies';
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

    $dr = DRClient::getInstance()->find($arguments['id']);

    foreach($dr->recolte->getAppellations() as $appellation) {
      if($appellation->getDplc() - $appellation->getLies() < 0) {
        echo $dr->_id.";".$appellation->getHash().";".$appellation->getDplc().";".$appellation->getLies()."\n";
      }
    }
  }
}
