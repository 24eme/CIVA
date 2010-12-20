<?php

class maintenanceDRSearchDemoninationTask extends sfBaseTask
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
    $this->name             = 'dr-search-denomination';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceStatsQuantitesDR|INFO] task does things.
Call it with:

  [php symfony maintenanceStatsQuantitesDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set("memory_limit", "512M");

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $expressions = array('1ER CRU', '1 ER CRU', 'PREMIER CRU');
    $values = array();
    foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            if ($dr->isValideeTiers()) {
                foreach($dr->recolte->getAppellations() as $appellation) {
                    foreach($appellation->getLieux() as $lieu) {
                        foreach($lieu->getCepages() as $cepage) {
                            foreach($cepage->getDetail() as $detail) {
                                foreach($expressions as $expression) {
                                    if (strpos(strtoupper($detail->denomination), strtoupper($expression)) !== false) {
                                        $this->logSection($dr->cvi, $detail->denomination);
                                        if (!array_key_exists($dr->cvi, $values)) {
                                            $values[$dr->cvi] = array($dr->cvi, $cepage->getConfig()->getLibelle() . ' : ' . $detail->denomination);
                                        } else {
                                            $values[$dr->cvi][] = $cepage->getConfig()->getLibelle() . ' : ' . $detail->denomination;
                                        }
                                    }
                                }
                                
                            }
                        }
                    }
                    
                }
            }
            unset($dr);
    }

    $content_csv = Tools::getCsvFromArray($values);
    $filedir = sfConfig::get('sf_web_dir').'/';
    $filename = 'CVI-DR-'.$options['campagne'].'-PREMIER-CRU.csv';
    file_put_contents($filedir.$filename, $content_csv);
    $this->logSection("created", $filedir.$filename);

  }
}
