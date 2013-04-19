<?php

class maintenanceDRListTask extends sfBaseTask
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
    $this->name             = 'dr-list';
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
    $values = array();
    foreach ($dr_ids as $id) {
            $dr = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);
            if (isset($dr->validee) && $dr->validee && isset($dr->import_db2) && $dr->import_db2 == 1) {
                $values[][] = $dr->cvi;
            }
    }

    $content_csv = Tools::getCsvFromArray($values);
    $filedir = sfConfig::get('sf_web_dir').'/';
    $filename = 'CVI-DR-'.$options['campagne'].'.csv';
    file_put_contents($filedir.$filename, $content_csv);
    $this->logSection("created", $filedir.$filename);
    // add your code here
  }
}
