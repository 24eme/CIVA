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

        $ids_tiers = acCouchdbManager::getClient("Tiers")->getAllIds();
        $ids_compte_updated = array();
        $ids_compte_new = array();

        foreach ($ids_tiers as $id) {
            $tiers = acCouchdbManager::getClient()->find($id, acCouchdbClient::HYDRATE_JSON);
            $compte = acCouchdbManager::getClient()->find("COMPTE-" . $tiers->cvi);

            if (!$compte) {
                $compte = new CompteProxy();
                $compte->login = $tiers->cvi;
                $compte->constructId();
                if ($rec = acCouchdbManager::getClient()->find("REC-" . $tiers->cvi)) {
                    $compte->compte_reference = $rec->compte;
                } elseif ($met = acCouchdbManager::getClient()->find("MET-" . $tiers->civaba)) {
                    $compte->compte_reference = $met->compte;
                } else {
                    $this->logSection("tiers introuvable", $id, null, "ERROR");
                    continue;
                }
                $this->logSection("création d'un compte proxy", $id);
            }

            if (isset($tiers->gamma)) {
                $met = acCouchdbManager::getClient()->find('MET-' . $tiers->civaba);
                if ($met) {
                    $met->add('gamma', $tiers->gamma);
                    if ($met->isModified()) {
                        $this->logSection("gamma", $met->get('_id'));
                    }
                    $met->save();
                }
            }

            $compte->mot_de_passe = preg_replace("/^([0-9]{4})$/", "{OUBLIE}$0", $tiers->mot_de_passe);


            if (!$compte->email) {
                $compte->email = $tiers->email;
            }

            if (!$compte->email && $compte->getStatut() == 'INSCRIT') {
                $this->logSection("email", $compte->get('_id'), null, 'ERROR');
            }

            if ($compte->isModified()) {
                $this->logSection("compte", $id);
            }
            $compte->save();
            $ids_compte_updated[] = $compte->get('_id');
        }

        $ids_compte = acCouchdbManager::getClient("_Compte")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $export = new ExportCsv();
        foreach ($ids_compte as $id_compte) {
            if (!in_array($id_compte, $ids_compte_updated)) {
                $compte = acCouchdbManager::getClient()->find($id_compte);
                $export->add(array(
                    "id" => $compte->_id,
                    "login" => $compte->login,
                    "mot_de_passe" => $compte->mot_de_passe,
                    "email" => $compte->email,
                    "nom" => $compte->getNom(),
                    "statut" => $compte->getStatut(),
                ));
                $ids_compte_new[] = $id_compte;
                $this->logSection("nouveau compte", $id_compte);
            }
        }
        echo $export->output();
        
        echo "\n\n\n-------------------------------------------\n\n\n";
        
        $export = new ExportCsv();
        $mets = acCouchdbManager::getClient("MetteurEnMarche")->getAll(acCouchdbClient::HYDRATE_JSON);
        foreach ($mets as $met) {

            if ($met->cvi) {
                $tiers_civaba = acCouchdbManager::getClient()->find("TIERS-C" . $met->civaba, acCouchdbClient::HYDRATE_JSON);
                $tiers_cvi = acCouchdbManager::getClient()->find("TIERS-" . $met->cvi, acCouchdbClient::HYDRATE_JSON);
                if ($tiers_civaba) {
                    
                } elseif ($tiers_cvi && $tiers_cvi->civaba == $met->civaba) {
                    $compte = acCouchdbManager::getClient()->find("COMPTE-C" . $met->civaba);
                    $compte_acheteur = acCouchdbManager::getClient()->find("COMPTE-" . $met->cvi);
                    if ($compte) {
                        /*$this->log($met->_id);
                        $this->logSection("metteur", $met->cvi);
                        $this->logSection("ancien login", $tiers_cvi->cvi);
                        $this->logSection("nouveau login", "C" . $met->civaba);*/

                        $export->add(array(
                            "ancien_tiers" => $tiers_cvi->_id,
                            "ancien_login" => $tiers_cvi->cvi,
                            "acheteur_id" => $compte_acheteur->_id,
                            "acheteur_login" => $compte_acheteur->login,
                            "acheteur_mot_de_passe" => $compte_acheteur->mot_de_passe,
                            "acheteur_email" => $compte_acheteur->email,
                            "acheteur_nom" => $compte_acheteur->getNom(),
                            "acheteur_statut" => $compte_acheteur->getStatut(),
                            "met_id" => $compte->_id,
                            "met_login" => $compte->login,
                            "met_mot_de_passe" => $compte->mot_de_passe,
                            "met_email" => $compte->email,
                            "met_nom" => $compte->getNom(),
                            "met_statut" => $compte->getStatut(),
                        ));
                    }
                }
            }
        }
        echo $export->output();

    }

}
