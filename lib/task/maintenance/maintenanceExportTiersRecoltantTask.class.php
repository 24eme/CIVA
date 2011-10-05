<?php

class maintenanceExportRecoltantsTask extends sfBaseTask {

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
        $this->name = 'export-recoltants';
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

        $recoltants = sfCouchdbManager::getClient('Recoltant')->getAll(sfCouchdbClient::HYDRATE_JSON);
        $csv = new ExportCsv(array("cvi" => "Numéro CVI",
                "siret" => "Numéro Siret",
                "login" => "Login",
                "mot_de_passe" => "Code de création",
                "civilite" => "Civilité",
                "nom" => "Nom Prénom",
                "adresse" => "Adresse",
                "code postal" => "Code postal",
                "commune" => "Commune"
            ));

        $validation = array(
            "cvi" => array("required" => true, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => true, "type" => "string"),
            "code postal" => array("required" => true, "type" => "string"),
            "commune" => array("required" => true, "type" => "string")
        );

        foreach ($recoltants as $rec) {
            if (!$rec->compte) {
                $this->logSection($rec->cvi, "COMPTE VIDE", null, 'ERROR');
                continue;
            }
            $compte = sfCouchdbManager::getClient()->retrieveDocumentById($rec->compte, sfCouchdbClient::HYDRATE_JSON);
            if ($compte) {
            $intitule = $rec->intitule;
            $nom = $rec->nom;
            $adresse = $rec->siege;
            if (!$adresse->adresse) {
                if ($rec->exploitant->nom) {
                    $intitule = $rec->exploitant->sexe;
                    $nom = $rec->exploitant->nom;
                }
                $adresse = $rec->exploitant;
            }
            
            if (substr($compte->mot_de_passe, 0, 6) == "{TEXT}") {
                $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compte->mot_de_passe);
            } else {
                $mot_de_passe = "Compte déjà créé";
            }
            
            try {
               $csv->add(array("cvi" => $rec->cvi,
                "siret" => $rec->siret,
                "login" => $compte->login,
                "mot_de_passe" => $mot_de_passe,
                "civilite" => $intitule,
                "nom" => $nom,
                "adresse" => $adresse->adresse,
                "code postal" => $adresse->code_postal,
                "commune" => $adresse->commune
            ), $validation); 
            } catch (Exception $exc) {
                $this->logSection($rec->cvi, $exc->getMessage(), null, 'ERROR');
            }
            } else {
                $this->logSection($rec->cvi, "COMPTE INEXISTANT", null, 'ERROR');
            }
            
        }

        echo $csv->output(false);
    }

}
