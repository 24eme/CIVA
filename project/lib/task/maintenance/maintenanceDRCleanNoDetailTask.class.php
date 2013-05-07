<?php

class maintenanceDRCleanNoDetailTask extends sfBaseTask
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
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'export cvi for a campagne', '2010'),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-clean-no-detail';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceListDR|INFO] task does things.
Call it with:

  [php symfony maintenanceListDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set("memory_limit", "512M");
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $nb_cleaned = 0;
    $values = array();
    foreach ($dr_ids as $id) {
           $dr_json = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);
            if (isset($dr_json->validee) && $dr_json->validee && (!isset($dr_json->import_db2) || $dr_json->import_db2 != 1)) {
                $dr = acCouchdbManager::getClient("DR")->find($id);
                if ($dr->clean()) {
                    $dr->save();
                    $this->logSection('dr cleaned', $id);
                    $nb_cleaned++;
                }

            }
    }

    $this->logSection("nb cleaned", $nb_cleaned);
    // add your code here
  }
}
