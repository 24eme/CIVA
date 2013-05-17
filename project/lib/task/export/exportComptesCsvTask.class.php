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

        $comptes_json = acCouchdbManager::getClient()
                                      ->getView("COMPTE", "tous")->rows;

        $comptes = array();

        foreach($comptes_json as $compte) {
            $comptes[$compte->value->_id] = $compte->value;
            if (substr($compte->value->mot_de_passe, 0, 6) == "{TEXT}") {
                $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compte->value->mot_de_passe);
            } elseif($compte->value->mot_de_passe) {
                $mot_de_passe = "Compte déjà créé";
            } else {
                $mot_de_passe = "Compte désactivé";
            }
            $comptes[$compte->value->_id]->code_creation = $mot_de_passe;
            $comptes[$compte->value->_id]->tiers_object = array();
        }

        $tiers = array();
        $tiers_types = array("Recoltant", "MetteurEnMarche", "Acheteur");
        foreach ($tiers_types as $tiers_type) {
            $tiers = array_merge($tiers, acCouchdbManager::getClient($tiers_type)->getAll(acCouchdbClient::HYDRATE_JSON)->getDocs());
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
                    "date_creation" => "Date de création",
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
                    "telephone" => "Téléphone",
                    "fax" => "Fax",
                    "web" => "Site internet",
                    "exploitant_sexe" => "Sexe de l'exploitant",
                    "exploitant_nom" => "Nom de l'exploitant",
                    "exploitant_adresse" => "Adresse de l'exploitant",
                    "exploitant_code_postal" => "Code postal de l'exploitant",
                    "exploitant_commune" => "Commune de l'exploitant",
                    "exploitant_telephone" => "Téléphone de l'exploitant"

                ));

        $validation = array(
            "type" => array("required" => true, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "statut" => array("required" => true, "type" => "string"),
            "date_creation" => array("required" => false, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "email" => array("required" => false, "type" => "string"),
            "cvi" => array("required" => false, "type" => "string"),
            "civaba" => array("required" => false, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "qualite" => array("required" => false, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => false, "type" => "string"),
            "code_postal" => array("required" => false, "type" => "string"),
            "commune" => array("required" => false, "type" => "string"),
            "telephone" => array("required" => false, "type" => "string"),
            "fax" => array("required" => false, "type" => "string"),
            "web" => array("required" => false, "type" => "string"),
            "exploitant_sexe" => array("required" => false, "type" => "string"),
            "exploitant_nom" => array("required" => false, "type" => "string"),
            "exploitant_adresse" => array("required" => false, "type" => "string"),
            "exploitant_code_postal" => array("required" => false, "type" => "string"),
            "exploitant_commune" => array("required" => false, "type" => "string"),
            "exploitant_telephone" => array("required" => false, "type" => "string")
        );

        $validation_proxy = $validation;
        $validation_proxy["nom"]["required"] = false;

        foreach ($comptes as $id_compte => $compte) {
            
            if ($compte->type == "CompteVirtuel") {
                $csv->add(array(
                        "type" => "Virtuel",
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "date_creation" => null,
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
                        "telephone" => null,
                        "fax" => null,
                        "web" => null,
                        "exploitant_sexe" => null,
                        "exploitant_nom" => null,
                        "exploitant_adresse" => null,
                        "exploitant_code_postal" => null,
                        "exploitant_commune" => null,
                        "exploitant_telephone" => null
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
                $telephones = array();
                $faxs = array();
                $webs = array();
                $sexes_exploitant = array();
                $noms_exploitant = array();
                $addresses_exploitant = array();
                $code_postals_exploitant = array();
                $communes_exploitant = array();
                $telephones_exploitant = array();

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
                    $telephone = isset($tiers->telephone) ? $tiers->telephone : null;
                    if(!$telephone && isset($tiers->exploitant->telephone)) {
                        $telephone = $tiers->exploitant->telephone;
                    }
                    $telephones[] = $telephone;
                    $faxs[] = isset($tiers->fax) ? $tiers->fax : null;
                    $webs[] = isset($tiers->web) ? $tiers->web : null;
                    $addresses[] = isset($adresse->adresse) ? $adresse->adresse : null;
                    $code_postals[] = isset($adresse->code_postal) ? $adresse->code_postal : null;
                    $communes[] = isset($adresse->commune) ? $adresse->commune : null;
                    $sexes_exploitant[] = isset($tiers->exploitant->sexe) ? $tiers->exploitant->sexe : null;
                    $noms_exploitant[] = isset($tiers->exploitant->nom) ? $tiers->exploitant->nom : null;
                    $addresses_exploitant[] = isset($tiers->exploitant->adresse) ? $tiers->exploitant->adresse : null;
                    $code_postals_exploitant[] = isset($tiers->exploitant->code_postal) ? $tiers->exploitant->code_postal : null;
                    $communes_exploitant[] = isset($tiers->exploitant->commune) ? $tiers->exploitant->commune : null;
                    $telephones_exploitant[] = isset($tiers->exploitant->telephone) ? $tiers->exploitant->telephone : null;
                }

                $csv->add(array(
                        "type" => implode('|', $types),
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "date_creation" => isset($compte->date_creation) ? $compte->date_creation : null,
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
                        "telephone" => implode('|', $telephones),
                        "fax" => implode('|', $faxs),
                        "web" =>implode('|', $webs),
                        "exploitant_sexe" => implode('|', $sexes_exploitant),
                        "exploitant_nom" => implode('|', $noms_exploitant),
                        "exploitant_adresse" => implode('|', $addresses_exploitant),
                        "exploitant_code_postal" => implode('|', $code_postals_exploitant),
                        "exploitant_commune" => implode('|', $communes_exploitant),
                        "exploitant_telephone" => implode('|', $telephones_exploitant),
                            ), $validation);

            } elseif($compte->type == "CompteProxy") {
                $csv->add(array(
                        "type" => "Proxy:".$comptes[$compte->compte_reference]->login,
                        "login" => $compte->login,
                        "statut" => $compte->statut,
                        "date_creation" => null,
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
                        "telephone" => null,
                        "fax" => null,
                        "web" => null,
                        "exploitant_sexe" => null,
                        "exploitant_nom" => null,
                        "exploitant_adresse" => null,
                        "exploitant_code_postal" => null,
                        "exploitant_commune" => null,
                        "exploitant_telephone" => null,
                            ), $validation_proxy);
            }
        }

        echo $csv->output(false);
    }
    
}
