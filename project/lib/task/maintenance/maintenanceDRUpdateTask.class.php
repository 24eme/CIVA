<?php

class maintenanceDRUpdateTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
          new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'dr-update';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $dr_ids = acCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        foreach ($dr_ids as $id) {
            $dr = acCouchdbManager::getClient()->find($id);
            $this->logSection('try', $dr->get('_id'));
            //if ($dr->exist('modifiee')) {
                //$modifiee = $dr->get('modifiee');
                //$dr->remove('modifiee');
                //$dr->update();
                //$dr->add('modifiee', $modifiee);
              //}
              //$dr->clean();
                if ($dr->isModified()) {
                    $this->logSection('validaded updated', $dr->get('_id'));
                }

                $dr->save();
            /*} else {
                //$dr->update();
                if ($dr->isModified()) {
                    $this->logSection('updated', $dr->get('_id'));
                }
                $dr->save();
            }*/
            /* $dr = acCouchdbManager::getClient()->find($id);

              foreach($dr->recolte->getAppellations() as $appellation) {
              foreach($appellation->getLieux() as $lieu) {

              foreach($lieu->acheteurs as $types) {
              foreach($types as $acheteur) {
              if(round($acheteur->dontdplc, 2) != $acheteur->dontdplc) {
              $this->logSection("DR", $id);
              $this->logSection("acheteur", $acheteur->dontdplc);
              $this->logSection("dplc", $lieu->getDplc());
              }
              if ($acheteur->dontdplc == $lieu->getTotalDontDplcRecapitulatifVente()) {
              if ($lieu->getTotalSuperficieRecapitulatifVente() == $lieu->getTotalSuperficie() && $acheteur->dontdplc != $lieu->getDplc()) {
              $this->logSection("DR", $id);
              $this->logSection("total", $lieu->getTotalDontDplcRecapitulatifVente());
              $this->logSection("acheteur", $acheteur->dontdplc);
              $acheteur->dontdplc = $lieu->getDplc();
              $this->logSection("new acheteur", $acheteur->dontdplc);
              $this->logSection("lieu", $lieu->getDplc());
              $this->log("--------------------------");

              }
              }
              }
              }
              }
              } */
        }
        // add your code here
    }

}
