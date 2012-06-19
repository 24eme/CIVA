<?php

class maintenanceAcheteursDoubleTask extends sfBaseTask
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
    $this->name             = 'acheteurs-double';
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

    $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($options['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

    foreach ($dr_ids as $id) {
            $is_double = false;
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($id);

            foreach($dr->recolte->getAppellations() as $appellation) {
                foreach($appellation->getLieux() as $lieu) {
                    $cvi_acheteurs = array();
                    foreach($lieu->acheteurs as $type => $acheteur_type) {
                        foreach($acheteur_type as $cvi => $acheteur) {
                            if (!array_key_exists($cvi, $cvi_acheteurs)) {
                                $cvi_acheteurs[$cvi] = 1;
                            } else {
                                $is_double = true;
                            }
                        }
                    }
                }
            }

            if ($is_double) {
                $this->logSection('DR', $dr->_id);
            }
            
    }
    // add your code here
  }
}
