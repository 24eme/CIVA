<?php

class exportTiersCsvTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'export';
        $this->name = 'tiers-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [exportComptesCsv|INFO] task does things.
Call it with:

  [php symfony exportComptesCsvTask|INFO]
EOF;
    }

     protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids = array_merge(sfCouchdbManager::getClient('Recoltant')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds(),
                           sfCouchdbManager::getClient('MetteurEnMarche')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds(),
                           sfCouchdbManager::getClient('Acheteur')->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds());

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

        $csv = new ExportCsv(array_keys($modele));

        foreach($ids as $id) {
            $tiers = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            $line = $this->makeLine($this->jsonToSimpleArray($tiers), $modele);
            if (isset($tiers->compte[0])) {
                $compte = sfCouchdbManager::getClient()->retrieveDocumentById($tiers->compte[0]);
                $line['email'] = $compte->email;
            }
            $csv->add($line);
        }

        echo $csv->output();
    }

    protected function makeLine($simpleArray, $modele) {
        $line = $modele;
        foreach($simpleArray as $key => $item) {
            if (array_key_exists($key, $modele)) {
                $line[$key] = $item;
            }
        }
        return $line;
    }

    protected function jsonToSimpleArray($json, $prefix = null) {
        $simpleArray = array();

        foreach($json as $key => $item) {
            if (is_array($item) || $item instanceof stdClass) {
                $simpleArray = array_merge($simpleArray, $this->jsonToSimpleArray($item, $key.'/')); 
            } else {
                $simpleArray[$prefix.$key] = $item;
            }
        }

        return $simpleArray;
    }

}


