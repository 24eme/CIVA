<?php

class exportComptesCsvTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('tiers_types', sfCommandArgument::IS_ARRAY, 'Type du tiers : Recoltant|MetteurEnMarche|Acheteur', array("Recoltant", "MetteurEnMarche", "Acheteur")),
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

        $tiers = array();
        foreach ($arguments['tiers_types'] as $tiers_type) {
            $tiers = array_merge($tiers, sfCouchdbManager::getClient($tiers_type)->getAll(sfCouchdbClient::HYDRATE_JSON)->getDocs());
        }

        $comptes = array();
        foreach ($tiers as $t) {
            if (count($t->compte) == 0) {
                $this->logSection($t->cvi, "COMPTE VIDE", null, 'ERROR');
                continue;
            }
            foreach ($t->compte as $id_compte) {
                $comptes[$id_compte][] = $t;
            }
        }

        $csv = new ExportCsv(array(
                    "type" => "Type",
                    "login" => "Login",
                    "statut" => "Statut",
                    "mot_de_passe" => "Code de création",
                    "cvi" => "Numéro CVI",
                    "civaba" => "Numéro CIVABA",
                    "siret" => "Numéro Siret",
                    "qualite" => "Qualité",
                    "civilite" => "Civilité",
                    "nom" => "Nom Prénom",
                    "adresse" => "Adresse",
                    "code postal" => "Code postal",
                    "commune" => "Commune"
                ));

        $validation = array(
            "type" => array("required" => true, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "statut" => array("required" => true, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "cvi" => array("required" => false, "type" => "string"),
            "civaba" => array("required" => false, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "qualite" => array("required" => false, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => false, "type" => "string"),
            "code postal" => array("required" => false, "type" => "string"),
            "commune" => array("required" => false, "type" => "string")
        );

        foreach ($comptes as $tiers_c) {
            foreach ($tiers_c as $t) {
                $compte = sfCouchdbManager::getClient()->retrieveDocumentById($t->compte, sfCouchdbClient::HYDRATE_JSON);
                if ($compte) {
                    $intitule = $t->intitule;
                    $nom = $t->nom;
                    $adresse = $t->siege;
                    if (!$adresse->adresse && isset($t->exploitant)) {
                        if ($t->exploitant->nom) {
                            $intitule = $t->exploitant->sexe;
                            $nom = $t->exploitant->nom;
                        }
                        $adresse = $t->exploitant;
                    }

                    if (substr($compte->mot_de_passe, 0, 6) == "{TEXT}") {
                        $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compte->mot_de_passe);
                    } else {
                        $mot_de_passe = "Compte déjà créé";
                    }

                    try {
                        $csv->add(array(
                            "type" => $t->type,
                            "login" => $compte->login,
                            "statut" => $compte->statut,
                            "mot_de_passe" => $mot_de_passe,
                            "cvi" => $this->getTiersField($t, 'cvi'),
                            "civaba" => $this->getTiersField($t, 'civaba'),
                            "siret" => $this->getTiersField($t, 'siret'),
                            "qualite" => $this->getTiersField($t, 'qualite'),
                            "civilite" => $intitule,
                            "nom" => $nom,
                            "adresse" => $adresse->adresse,
                            "code postal" => $adresse->code_postal,
                            "commune" => $adresse->commune
                                ), $validation);
                    } catch (Exception $exc) {
                        $this->logSection($t->cvi, $exc->getMessage(), null, 'ERROR');
                    }
                } else {
                    $this->logSection($t->cvi, "COMPTE INEXISTANT", null, 'ERROR');
                }
            }
        }

        echo $csv->output(false);
    }

    protected function getTiersField($tiers, $field, $default = null) {
        $value = $default;
        if (isset($tiers->{$field})) {
            $value = $tiers->{$field};
        }
        return $value;
    }

}
