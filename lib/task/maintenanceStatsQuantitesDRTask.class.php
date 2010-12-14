<?php

class maintenanceDRStatsQuantitesTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi for a campagne', '2010'),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-stats-quantites';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceStatsQuantitesDR|INFO] task does things.
Call it with:

  [php symfony maintenanceStatsQuantitesDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set("memory_limit", "256M");

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $values = array();
    $appellations = array();
    foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            if ($dr->isValideeTiers()) {
                foreach($dr->recolte->getAppellations() as $key => $appellation) {
                    $appellations[$key]['superficie'] = $appellation->getTotalSuperficie();
                    $appellations[$key]['volume'] = $appellation->getTotalVolume();
                    $appellations[$key]['name'] = $appellation->getLibelle();
                }
            }
    }

    foreach($appellations as $appellation) {
        $this->log($appellation['name']);
        $this->logSection('superficie', $appellation['superficie'].' ares');
        $this->logSection('volume', $appellation['volume'].' hl');
    }
    // add your code here
  }
}
