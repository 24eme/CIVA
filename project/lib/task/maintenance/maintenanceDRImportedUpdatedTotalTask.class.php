<?php

class maintenanceDRImportedUpdatedTotalTask extends sfBaseTask
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
    $this->name             = 'dr-imported-updated-total';
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
    $nb_updated = 0;
    $values = array();
    foreach ($dr_ids as $id) {
           $dr_json = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);
            if (isset($dr_json->validee) && $dr_json->validee && isset($dr_json->import_db2) && $dr_json->import_db2 == 1 && (!isset($dr_json->modifiee) || $dr_json->validee != $dr_json->modifiee)) {
                $dr = acCouchdbManager::getClient("DR")->find($id);
                /*if ($dr->clean()) {
                    $dr->save();
                    $this->logSection('dr updated', $id);
                    $nb_updated++;
                }*/
                $updated = false;
                foreach($dr->recolte->getAppellations() as $appellation) {
                    foreach($appellation->getLieux() as $lieu) {
                         if (round($lieu->getVolumeRevendique(),2) != round($lieu->getVolumeRevendique(true),2) || round($lieu->getDplc(),2) != round($lieu->getDplc(true),2)) {
                            $updated = true;
                            $lieu->volume_revendique = $lieu->getVolumeRevendique(true);
                            $lieu->dplc = $lieu->getDplc(true);
                         }
                    }
                    if (round($appellation->getTotalVolume(),2) != round($appellation->getTotalVolume(true),2) || round($appellation->getTotalSuperficie(),2) != round($appellation->getTotalSuperficie(true),2)) {
                        $updated = true;
                        $appellation->total_volume = $appellation->getTotalVolume(true);
                        $appellation->total_superficie = $appellation->getTotalSuperficie(true);
                        $this->log($appellation->getKey());
                    }
                }
                if($updated) {
                    $dr->save();
                    $nb_updated++;
                    $this->logSection('dr updated', $id);
                }
            }
    }

    $this->logSection("nb updated", $nb_updated);
    // add your code here
  }
}
