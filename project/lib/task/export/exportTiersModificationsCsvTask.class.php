<?php

class exportTiersModificationsCsv extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('numero_sequence', sfCommandArgument::REQUIRED, 'Numéro de séquence Couchdb')
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('older', null, sfCommandOption::PARAMETER_REQUIRED, 'Older Version', false),
        ));

        $this->namespace = 'export';
        $this->name = 'tiers-modifications-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportTiersModificationsCsv|INFO] task does things.
Call it with:

  [php symfony exportTiersModificationsCsvTask|INFO]
EOF;
    }

     protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $changes = acCouchdbManager::getClient()->since($arguments['numero_sequence'])->getChanges()->results;

        $modele = array(
            "_id" => null,
            "type" => null,
            "db2/num" => null,
            "db2/no_stock" => null,
            "cvi" => null,
            "civaba" => null,
            "cvi_acheteur" => null,
            "intitule" => null,
            "no_accises" => null,
            "nom" => null,
            "commune" => null,
            "declaration_commune" => null,
            "declaration_insee" => null,
            "email" => null,
            "siren" => null,
            "siret" => null,
            "telephone" => null,
            "fax" => null,
            "qualite" => null,
            "categorie" => null,
            "cave_cooperative" => null,
            "web" => null,
            "exploitant/adresse" => null,
            "exploitant/code_postal" => null,
            "exploitant/commune" => null,
            "exploitant/date_naissance" => null,
            "exploitant/nom" => null,
            "exploitant/sexe" => null,
            "exploitant/telephone" => null,
            "siege/adresse" => null,
            "siege/code_postal" => null,
            "siege/commune" => null,
            "siege/insee_commune" => null,
            );

        $types = array("REC", "MET", "ACHAT");


        $csv = new ExportCsv(array_keys($modele));

        foreach($changes as $change) {
            if(!preg_match(sprintf("/^(%s)-/", implode("|", $types)), $change->id)) {
                
                continue;
            }

            $tiers = acCouchdbManager::getClient()->find($change->id, acCouchdbClient::HYDRATE_JSON);
            if($tiers->statut == _TiersClient::STATUT_INACTIF) {
                
                continue;
            }

            $tiers_object = new acCouchdbJsonNative($tiers);
            $tiers_array = $tiers_object->toFlatArray();

            $tiers_object_old = new acCouchdbJsonNative($this->getOldDoc($tiers));
            $tiers_array_old = $tiers_object_old->toFlatArray();

            if($tiers_object->equal($tiers_object_old)) {

                continue;
            }

            $diffs = $tiers_object->diff($tiers_object_old);

            foreach($diffs as $key => $diff) {
                $tiers_array[$key] = sprintf("*%s (%s)", $tiers_array[$key], isset($tiers_array_old[$key]) ? $tiers_array_old[$key] : null);
            }

            $csv->add($this->makeLine($tiers_array, $modele));
        }

        echo $csv->output();
    }

    protected function getOldDoc($tiers) {
        $doc = null;
        $id = $tiers->_id;

        if(isset($tiers->db2->import_revision)) {
            $doc = acCouchdbManager::getClient()->rev($tiers->import_revision)->find($id, acCouchdbClient::HYDRATE_JSON); 

            if($doc) {
                return $doc;
            }
        }

        $data_revs = acCouchdbManager::getClient()->revs_info(true)->find($id, acCouchdbClient::HYDRATE_JSON);
        $revs = $data_revs->_revs_info;
        $i = count($revs)-1;
        while(!$doc && $i >= 0) {
            $rev = $revs[$i]->rev;
            $doc = acCouchdbManager::getClient()->rev($rev)->find($id, acCouchdbClient::HYDRATE_JSON);
            $i--;
        }
        if(!$doc) {
            throw new sfException("Doc non trouvé, c'est pas normal");
        }

        return $doc;
    }

    protected function makeLine($simpleArray, $modele) {
        $line = $modele;
        foreach($modele as $key => $item) {
            if (array_key_exists("/".$key, $simpleArray)) {
                $line[$key] = $simpleArray["/".$key];
            }
        }
        return $line;
    }

}


