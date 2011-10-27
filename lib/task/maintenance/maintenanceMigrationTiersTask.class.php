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
The [maintenanceImportGammaLogin|INFO] task does things.
Call it with:

  [php symfony maintenanceImportGammaLogin|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_tiers = array_merge(sfCouchdbManager::getClient("Recoltant")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds(), sfCouchdbManager::getClient("MetteurEnMarche")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds(), sfCouchdbManager::getClient("Acheteur")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds());
        foreach ($ids_tiers as $id_tiers) {
            $tiers = sfCouchdbManager::getClient()->retrieveDocumentById($id_tiers, sfCouchdbClient::HYDRATE_JSON);
            $t = sfCouchdbManager::getClient()->retrieveDocumentById($id_tiers, sfCouchdbClient::HYDRATE_DOCUMENT);
            if (is_string($tiers->compte)) {
                $t->remove('compte');
                $t->add('compte');
                $t->compte->add(null, $tiers->compte);
                if($t->isModified()) {
                        $this->logSection($t->get('_id'), "updated compte");
                }
            }
            if(isset($tiers->gamma) && is_string($tiers->gamma)) {
                    $t->remove('gamma');
                    $t->add('gamma');
                    $t->gamma->statut = $met->gamma;
                    if($t->isModified()) {
                        $this->logSection($t->get('_id'), "updated gamma");
                    }
            }
            $t->save();
        }
    }

}
