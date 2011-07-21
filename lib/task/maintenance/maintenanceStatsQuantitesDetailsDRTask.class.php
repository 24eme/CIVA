<?php

class maintenanceDRStatsQuantitesDetailsTask extends sfBaseTask
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
      new sfCommandOption('import_db2', null, sfCommandOption::PARAMETER_REQUIRED, 'import db2', 1),
      // add your own options here
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'dr-stats-quantites-details';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [maintenanceStatsQuantitesDR|INFO] task does things.
Call it with:

  [php symfony maintenanceStatsQuantitesDR|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    ini_set("memory_limit", "2048M");

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $values = array();
    $values[] =  array('appellation', 'cepage', 'superficie', 'volume', 'volume_revendique', 'dplc');
    $nb_dr = 0;
    $dr_update_error = array();
    $dr_invalid = array();
    $dr_stats = array('superficie' => 0, 'volume' => 0, 'volume_revendique' => 0, 'dplc' => 0);
    $appellation_stats = array();
    $cepages_stats = array();
    foreach ($dr_ids as $id) {
            if($id == 'DR-7523700100-'.$options['campagne']) {
                continue;
            }
            $dr = sfCouchdbManager::getClient("DR")->retrieveDocumentById($id);
            if (!$options['import_db2'] && $dr->exist('import_db2') && $dr->import_db2 == 1) {
                continue;
            }
            try {
                if (!$dr->updated)
                    throw new Exception();
            } catch (Exception $e) {
                try {
                    $dr->update();
                    $dr->save();
                } catch (Exception $exc) {
                    $this->logSection("failed update", $dr->_id, null, "ERROR");
                    $dr_update_error[] = $id;
                    continue;
                }
            }
            
            if ($dr->isValideeTiers()) {
                
                foreach($dr->recolte->getAppellations() as $appellation) {
                    foreach($appellation->getLieux() as $lieu) {
                        foreach($lieu->getCepages() as $cepage) {
                            if (!array_key_exists($appellation->getKey(), $cepages_stats)) {
                                $cepages_stats[$appellation->getKey()] = array();
                            }
                            if (!array_key_exists($cepage->getKey(), $cepages_stats[$appellation->getKey()])) {
                                $cepages_stats[$appellation->getKey()][$cepage->getKey()] = array('superficie' => 0, 'volume' => 0, 'nom' => $cepage->getConfig()->getLibelle());
                            }
                            $cepages_stats[$appellation->getKey()][$cepage->getKey()]['superficie'] += $cepage->getTotalSuperficie();
                            $cepages_stats[$appellation->getKey()][$cepage->getKey()]['volume'] += $cepage->getTotalVolume();
                        }
                    }

                    if (!array_key_exists($appellation->getKey(), $appellation_stats)) {
                        $appellation_stats[$appellation->getKey()] = array('superficie' => 0, 'volume' => 0, 'volume_revendique' => 0, 'dplc' => 0, 'nom' => $appellation->getLibelle());
                    }

                    $appellation_stats[$appellation->getKey()]['superficie'] += $appellation->getTotalSuperficie();
                    $appellation_stats[$appellation->getKey()]['volume'] += $appellation->getTotalVolume();
                    $appellation_stats[$appellation->getKey()]['volume_revendique'] += $appellation->getVolumeRevendique();
                    $appellation_stats[$appellation->getKey()]['dplc'] += $appellation->getDplc();
                }
                $dr_stats['superficie'] += $dr->getTotalSuperficie();
                $dr_stats['volume'] += $dr->getTotalVolume();
                $dr_stats['volume_revendique'] += $dr->getVolumeRevendique();
                $dr_stats['dplc'] += $dr->getDplc();
                
                $nb_dr++;
                $this->logSection("calcul", $dr->_id);
            } else {
                $dr_invalid[] = $id;
            }
    }


    foreach($appellation_stats as $key_appellation => $appellation_stat) {
        foreach($cepages_stats[$key_appellation] as $cepage_stat) {
            $current_value = array();
                $current_value[] = $appellation_stat['nom'];
                $current_value[] = $cepage_stat['nom'];
                $current_value[] = round($cepage_stat['superficie'], 2);
                $current_value[] = round($cepage_stat['volume'], 2);
                $current_value[] = '';
                $current_value[] = '';
                $values[] = $current_value;
            }
            $current_value = array();
            $current_value[] = $appellation_stat['nom'];
            $current_value[] = 'TOTAL';
            $current_value[] = round($appellation_stat['superficie'], 2);
            $current_value[] = round($appellation_stat['volume'], 2);
            $current_value[] = round($appellation_stat['volume_revendique'], 2);
            $current_value[] = round($appellation_stat['dplc'], 2);
            $values[] = $current_value;
     }


    $current_value = array();
    $current_value[] = 'TOTAL';
    $current_value[] = 'TOTAL';
    $current_value[] = round($dr_stats['superficie'], 2);
    $current_value[] = round($dr_stats['volume'], 2);
    $current_value[] = round($dr_stats['volume_revendique'], 2);
    $current_value[] = round($dr_stats['dplc'], 2);
    $values[] = $current_value;

    $content_csv = Tools::getCsvFromArray($values);
    $filedir = sfConfig::get('sf_web_dir').'/';
    $filename = 'STATS-DR-'.$options['campagne'].'-'.$options['import_db2'].'.csv';
    file_put_contents($filedir.$filename, $content_csv);
    $this->logSection("created", $filedir.$filename);

    $this->logSection('finish', $nb_dr);
    if ($dr_update_error) {
        $this->logSection('update failed', implode(", ", $dr_update_error));
    }
    if ($dr_invalid) {
    $this->logSection('pas valider', implode(", ", $dr_invalid));
    }

    // add your code here
  }
}
