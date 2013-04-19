<?php

class maintenanceMigrationTiersTask extends sfBaseTask {

    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'maintenance';
        $this->name = 'migration-tiers';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenanceMigrationTiersTask|INFO] task does things.
Call it with:

  [php symfony maintenanceMigrationTiersTask|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_tiers = array_merge(acCouchdbManager::getClient("Recoltant")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds(), acCouchdbManager::getClient("MetteurEnMarche")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds(), acCouchdbManager::getClient("Acheteur")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds());
        foreach ($ids_tiers as $id_tiers) {
            $tiers = acCouchdbManager::getClient()->find($id_tiers, acCouchdbClient::HYDRATE_JSON);
            if (is_string($tiers->compte) || (isset($tiers->gamma) && is_string($tiers->gamma))) {
                $t = acCouchdbManager::getClient()->find($id_tiers, acCouchdbClient::HYDRATE_DOCUMENT);
            }

            if (is_string($tiers->compte)) {
                var_dump($tiers->compte);
                $this->log($tiers->_id);
                $t->remove('compte');
                $t->add('compte');
                $t->compte->add(null, $tiers->compte);
            }

            if (isset($tiers->gamma) && is_string($tiers->gamma)) {
                $t->remove('gamma');
                $t->add('gamma');
                $t->gamma->statut = $tiers->gamma;
            }

            if (isset($t)) {
                if ($t->isModified()) {
                    $this->logSection($t->get('_id'), "updated tiers");
                }
                $t->save();
            }
        }
    }

}
