<?php

class maintenanceTiersToCompteTask extends sfBaseTask {

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
        $this->name = 'tiers-to-compte';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenanceTiers2Compte|INFO] task does things.
Call it with:

  [php symfony maintenanceTiersToCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_tiers = sfCouchdbManager::getClient("Tiers")->getAllIds();

        foreach ($ids_tiers as $id) {
            $tiers = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            $compte = sfCouchdbManager::getClient()->retrieveDocumentById("COMPTE-" . $tiers->cvi);
            if (isset($tiers->import_db2_date)) {
                if (!$compte) {
                    $compte = new CompteProxy();
                    $compte->set('_id', 'COMPTE-' . $tiers->cvi);
                    if ($rec = sfCouchdbManager::getClient()->retrieveDocumentById("REC-" . $tiers->cvi)) {
                        $compte->compte_reference = $rec->compte;
                    } elseif ($met = sfCouchdbManager::getClient()->retrieveDocumentById("MET-" . $tiers->civaba)) {
                        $compte->compte_reference = $met->compte;
                    } else {
                        $this->log($id);
                    }
                    
                    $this->logSection("compte proxy", $id);
                }
                
                if(isset($tiers->gamma)) {
                   $met = sfCouchdbManager::getClient()->retrieveDocumentById('MET-'.$tiers->civaba);
                   if ($met) {
                        $met->add('gamma', $tiers->gamma);
                        $met->save();
                   }
                }
                
                $compte->login = $tiers->cvi;
                $compte->mot_de_passe = preg_replace("/^[0-9]{4}$/", "{OUBLIE}", $tiers->mot_de_passe);
                $compte->save();
            }
        }
    }

}
