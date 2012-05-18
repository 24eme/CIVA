<?php

class exportComptesCsvTask extends sfBaseTask {

    protected function configure() {
        $this->addArguments(array(  
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
        ));

        $this->namespace = 'export';
        $this->name = 'comptes-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony maintenanceExportTiersGammaTask|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $comptes_json = sfCouchdbManager::getClient()
                                      ->getView("COMPTE", "tous")->rows;

        $comptes = array();

        foreach($comptes_json as $compte) {
            $comptes[$compte->value->_id] = $compte->value;
            if (substr($compte->value->mot_de_passe, 0, 6) == "{TEXT}") {
                $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compte->value->mot_de_passe);
            } else {
                $mot_de_passe = "Compte déjà créé";
            }
            $comptes[$compte->value->_id]->code_creation = $mot_de_passe;
        }

        $tiers = array();
        $tiers_types = array("Recoltant", "MetteurEnMarche", "Acheteur");
        foreach ($tiers_types as $tiers_type) {
            $tiers = array_merge($tiers, sfCouchdbManager::getClient($tiers_type)->getAll(sfCouchdbClient::HYDRATE_JSON)->getDocs());
        }

        foreach ($tiers as $t) {
            if (count($t->compte) == 0) {
                $this->logSection($t->cvi, "COMPTE VIDE", null, 'ERROR');
                continue;
            }
            foreach ($t->compte as $id_compte) {
                $comptes[$id_compte]->tiers_object[] = $t;
            }
        }

        $csv = new ExportCsv(array(
                    "type" => "Type",
                    "login" => "Login",
                    "statut" => "Statut",
                    "mot_de_passe" => "Code de création",
                    "email" => "Email",
                    "cvi" => "Numéro CVI",
                    "civaba" => "Numéro CIVABA",
                    "siret" => "Numéro Siret",
                    "qualite" => "Qualité",
                    "civilite" => "Civilité",
                    "nom" => "Nom Prénom",
                    "adresse" => "Adresse",
                    "code postal" => "Code postal",
                    "commune" => "Commune",
                    "exploitant_sexe" => "Sexe de l'exploitant",
                    "exploitant_nom" => "Nom de l'exploitant"
                ));

        $validation = array(
            "type" => array("required" => true, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "statut" => array("required" => true, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "email" => array("required" => false, "type" => "string"),
            "cvi" => array("required" => false, "type" => "string"),
            "civaba" => array("required" => false, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "qualite" => array("required" => false, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => false, "type" => "string"),
            "code postal" => array("required" => false, "type" => "string"),
            "commune" => array("required" => false, "type" => "string"),
            "exploitant_sexe" => array("required" => false, "type" => "string"),
            "exploitant_nom" => array("required" => false, "type" => "string")
        );

        $validation_proxy = $validation;
        $validation_proxy["nom"]["required"] = false;

        foreach ($comptes as $id_compte => $compte) {
            
            if ($compte->type == "CompteVirtuel") {
                $csv->add(array(
                        "type" => "Virtuel",
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "mot_de_passe" => $compte->code_creation,
                        "email" => $compte->email,
                        "cvi" => null,
                        "civaba" => null,
                        "siret" => null,
                        "qualite" => null,
                        "civilite" => null,
                        "nom" => $compte->nom,
                        "adresse" => null,
                        "code postal" => isset($compte->code_postal) ? $compte->code_postal : null,
                        "commune" => isset($compte->commune) ? $compte->commune : null,
                        "sexe de l'exploitant" => null,
                        "nom de l'exploitant" => null,
                            ), $validation);

            } elseif($compte->type == "CompteTiers") {

                $types = array();
                $cvis = array();
                $civabas = array();
                $sirets = array();
                $qualites = array();
                $civilites = array();
                $noms = array();
                $addresses = array();
                $code_postals = array();
                $communes = array();
                $sexes_exploitant = array();
                $noms_exploitant = array();

                foreach($compte->tiers_object as $tiers) {
                    $types[] = $tiers->type;
                    $cvis[] = isset($tiers->cvi) ? $tiers->cvi : null;
                    $civabas[] = isset($tiers->civaba) ? $tiers->civaba : null;
                    $sirets[] = isset($tiers->siret) ? $tiers->siret : null;
                    $qualites[] = isset($tiers->qualite) ? $tiers->qualite : null;
                    $civilites[] = isset($tiers->intitule) ? $tiers->intitule : null;
                    $noms[] = isset($tiers->nom) ? $tiers->nom : null;
                    $adresse = $tiers->siege;
                    if (!$adresse->adresse && isset($tiers->exploitant)) {
                        $adresse = $tiers->exploitant;
                    }
                    $addresses[] = isset($adresse->adresse) ? $adresse->adresse : null;
                    $code_postals[] = isset($adresse->code_postal) ? $adresse->code_postal : null;
                    $communes[] = isset($adresse->commune) ? $adresse->commune : null;
                    $sexes_exploitant[] = isset($tiers->exploitant->sexe) ? $tiers->exploitant->sexe : null;
                    $noms_exploitant[] = isset($tiers->exploitant->nom) ? $tiers->exploitant->nom : null;
                }

                $csv->add(array(
                        "type" => implode('|', $types),
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "mot_de_passe" => $compte->code_creation,
                        "email" => $compte->email,
                        "cvi" => implode('|', $cvis),
                        "civaba" => implode('|', $civabas),
                        "siret" => implode('|', $sirets),
                        "qualite" => implode('|', $qualites),
                        "civilite" => implode('|', $civilites),
                        "nom" => implode('|', $noms),
                        "adresse" => implode('|', $addresses),
                        "code postal" => implode('|', $code_postals),
                        "commune" => implode('|', $communes),
                        "sexe de l'exploitant" => implode('|', $sexes_exploitant),
                        "nom de l'exploitant" => implode('|', $noms_exploitant),
                            ), $validation);
            } elseif($compte->type == "CompteProxy") {
                $csv->add(array(
                        "type" => "Proxy:".$comptes[$compte->compte_reference]->login,
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "mot_de_passe" => $compte->code_creation,
                        "email" => $compte->email,
                        "cvi" => null,
                        "civaba" => null,
                        "siret" => null,
                        "qualite" => null,
                        "civilite" => null,
                        "nom" => null,
                        "adresse" => null,
                        "code postal" => null,
                        "commune" => null,
                        "sexe de l'exploitant" => null,
                        "nom de l'exploitant" => null,
                            ), $validation_proxy);
            }
        }

        echo $csv->output(false);
    }
    
}
