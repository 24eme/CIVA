<?php

class tiersLierTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'tiers';
        $this->name = 'lier';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [tiersLier|INFO] task does things.
Call it with:

  [php symfony tiersLier|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $mets = acCouchdbManager::getClient("MetteurEnMarche")->getAll(acCouchdbClient::HYDRATE_JSON);
        $acheteurs = acCouchdbManager::getClient("Acheteur")->getAll(acCouchdbClient::HYDRATE_JSON);

        $cvi_acheteur_no_stock = array();

        foreach ($mets as $met) {
            if ($met->cvi_acheteur) {
                $cvi_acheteur_no_stock[$met->cvi_acheteur] = $met;
            }
        }

        foreach ($acheteurs as $acheteur) {
            $acheteur_object = acCouchdbManager::getClient()->find($acheteur->_id);
            if ($acheteur_object) {
                if (array_key_exists($acheteur->cvi, $cvi_acheteur_no_stock)) {
                    $met = $cvi_acheteur_no_stock[$acheteur->cvi];
                    $acheteur_object->db2->no_stock = $met->db2->no_stock;
                    $acheteur_object->db2->num = $met->db2->num;
                    if ($acheteur_object->isModified()) {
                        $this->logSection($acheteur->_id, "lier");
                    }
                } else {
                    $acheteur_object->db2->no_stock = "NOSTOCK" . $acheteur_object->cvi;
                    $acheteur_object->db2->num = "NOSTOCK" . $acheteur_object->cvi;
                    $this->logSection($acheteur->_id, "pas lier", null, 'ERROR');
                }
            }
            $acheteur_object->save();
        }

        // add your code here
    }

}
