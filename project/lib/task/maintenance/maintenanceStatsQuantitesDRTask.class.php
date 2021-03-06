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
    ini_set("memory_limit", "2048M");

    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    $values = array();
    $appellations = array();
    $nb_dr = 0;
    $total_volume = 0;
    $total_superficie = 0;
    $dr_update_error = array();
    $dr_invalid = array();
    foreach ($dr_ids as $id) {
            if($id == 'DR-7523700100-'.$options['campagne']) {
                continue;
            }
            $dr = acCouchdbManager::getClient("DR")->find($id);
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
                foreach($dr->recolte->getAppellations() as $key => $appellation) {
                    if (!array_key_exists($key, $appellations)) {
                        $appellations[$key] = array('superficie' => 0, 'volume' => 0, 'volume_revendique' => 0, 'dplc' => 0, 'name' => $appellation->getLibelle());
                    }
                    $appellations[$key]['superficie'] += $appellation->getTotalSuperficie();
                    $appellations[$key]['volume'] += $appellation->getTotalVolume();
                    $appellations[$key]['volume_revendique'] += $appellation->getVolumeRevendique();
                    $appellations[$key]['dplc'] += $appellation->getDplc();
                }
                $total_superficie += $dr->getTotalSuperficie();
                $total_volume += $dr->getTotalVolume();
                $nb_dr++;
                $this->logSection("calcul", $dr->_id);
            } else {
                $dr_invalid[] = $id;
            }
    }

    foreach($appellations as $appellation) {
        $this->log($appellation['name']);
        $this->logSection('superficie', $appellation['superficie'].' ares');
        $this->logSection('volume', $appellation['volume'].' hl');
        $this->logSection('volume_revendique', $appellation['volume_revendique'].' hl');
        $this->logSection('dplc', $appellation['dplc'].' hl');
    }
    $this->log('total');
    $this->logSection('superficie', $total_superficie.' ares');
    $this->logSection('volume', $total_volume.' hl');

    $this->logSection('finish', $nb_dr);
    $this->logSection('update failed', implode(", ", $dr_update_error));
    $this->logSection('pas valider', implode(", ", $dr_invalid));

    // add your code here
  }
}
