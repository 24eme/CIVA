<?php

class maintenanceCompteAcheteurTask extends sfBaseTask {

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

        $this->namespace = 'maintenance';
        $this->name = 'compte-acheteur';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenanceComteAcheteur|INFO] task does things.
Call it with:

  [php symfony maintenanceTiersToCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_acheteur = sfCouchdbManager::getClient("Acheteur")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $nb = 0;
        foreach($ids_acheteur as $id_acheteur) {
             
            $cvi = str_replace('ACHAT-', '', $id_acheteur);
            if($tiers = sfCouchdbManager::getClient()->retrieveDocumentById('TIERS-'.$cvi, sfCouchdbClient::HYDRATE_JSON)) {
                $compte = sfCouchdbManager::getClient()->retrieveDocumentById('COMPTE-'.$cvi);
                if ($met = sfCouchdbManager::getClient()->retrieveDocumentById('MET-'.$tiers->civaba, sfCouchdbClient::HYDRATE_JSON)) {
                    if (!$compte->tiers->exist($met->_id)) {
                        $met_compte = $compte->tiers->add($met->_id);
                        $met_compte->id = $met->_id;
                        $met_compte->type = $met->type;
                        $met_compte->nom = $met->nom;
                        $compte->save();
                        $this->log($compte->login);
                        $nb++;
                    }
                    if ($compte->statut == "INSCRIT") {
                       
                    }
                    
                }
            }
        }
        $this->log($nb);
    }

}
