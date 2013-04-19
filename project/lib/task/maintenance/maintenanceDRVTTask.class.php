<?php

class maintenanceDRVTTask extends sfBaseTask
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
      // add your own options here
      new sfCommandOption('delete', null, sfCommandOption::PARAMETER_REQUIRED, 'Delete', false),
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'Campagne', '2009'),
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'DRVT';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set('memory_limit', '128M');
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    if ($options['campagne'] > 2009) {
        throw new sfCommandException('Campagne must be inforior to 2010');
    }

    $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

    $nb_vt = 0;

    foreach ($dr_ids as $id) {
            $dr_json = acCouchdbManager::getClient()->getDoc($id);
            if (isset($dr_json->recolte->appellation_VINTABLE)) {
                if ($options['delete']) {
                    $dr = acCouchdbManager::getClient("DR")->find($id);
                    $dr->delete();
                    $this->logSection("delete", $dr_json->_id);
                }
                $nb_vt++;
            }
    }

    $this->log($nb_vt);
    // add your code here
  }
}
