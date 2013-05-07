<?php

class maintenanceAcheteursTask extends sfBaseTask
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
      new sfCommandOption('campagne', null, sfCommandOption::PARAMETER_REQUIRED, 'Campagne', '2010'),
    ));

    $this->namespace        = 'maintenance';
    $this->name             = 'acheteurs';
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

    $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

    foreach ($dr_ids as $id) {
            $updated = false;
            $dr_json = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
            if (isset($dr_json->updated) && $dr_json->updated == 1) {
                foreach($dr_json->recolte as $appellation_key => $appellation) {
                    foreach($appellation as $lieu_key => $lieu) {
                        if (strpos($lieu_key, 'lieu') !== false) {
                            $old_acheteurs = array();
                            foreach($lieu->acheteurs as $cvi => $acheteur) {
                                if (!in_array($cvi, array('negoces', 'cooperatives', 'mouts'))) {
                                    $old_acheteurs[$cvi] = $acheteur;
                                    unset($lieu->acheteurs->$cvi);
                                }
                            }
                            foreach($old_acheteurs as $cvi => $acheteur) {
                                $type = $acheteur->type_acheteur;
                                $dr_json->recolte->{$appellation_key}->{$lieu_key}->acheteurs->{$type}->{$cvi} = $acheteur;
                                $updated = true;
                            }
                        }
                    }
                }
            }
            if ($updated) {
                acCouchdbManager::getClient()->storeDoc($dr_json);
                $this->logSection('saved', $dr_json->_id);
                $dr = acCouchdbManager::getClient()->find($id);
                $dr->update();
                $dr->update();
                $dr->save();
                $this->logSection('updated', $dr_json->_id);
            }
    }
    // add your code here
  }
}
