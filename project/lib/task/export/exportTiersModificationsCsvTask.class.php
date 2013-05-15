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

        $changes = sfCouchdbManager::getClient()->since($arguments['numero_sequence'])->getChanges()->results;

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

        $tiers_modifies = array();

        foreach($changes as $change) {

            if(preg_match(sprintf("/^COMPTE-/", implode("|", $types)), $change->id)) {
                $compte = sfCouchdbManager::getClient()->retrieveDocumentById($change->id, sfCouchdbClient::HYDRATE_JSON);

                if(!isset($compte->tiers)) {

                    continue;
                }

                foreach($compte->tiers as $tiers) {
                    if(!array_key_exists($tiers->id, $tiers_modifies)) {
                        $tiers_modifies[$tiers->id] = sfCouchdbManager::getClient()->retrieveDocumentById($tiers->id, sfCouchdbClient::HYDRATE_JSON);
                    }

                    $tiers_modifies[$tiers->id]->email = $compte->email;
                }
            }

            if(!preg_match(sprintf("/^(%s)-/", implode("|", $types)), $change->id)) {
                
                continue;
            }

            if(!array_key_exists($change->id, $tiers_modifies)) {
               $tiers_modifies[$change->id] = sfCouchdbManager::getClient()->retrieveDocumentById($change->id, sfCouchdbClient::HYDRATE_JSON);;
            }
        }

        foreach($tiers_modifies as $tiers) {
            if($tiers->statut == _TiersClient::STATUT_INACTIF) {
                
                continue;
            }

            $tiers_object = new sfCouchdbJsonNative($tiers);
            $tiers_array = $tiers_object->toFlatArray();

            $tiers_object_old = new sfCouchdbJsonNative($this->getOldDoc($tiers));
            $tiers_array_old = $tiers_object_old->toFlatArray();

            if($tiers_object->equal($tiers_object_old)) {

                continue;
            }

            $diffs = $tiers_object->diff($tiers_object_old);

            $nb_diff = 0;
            foreach($diffs as $key => $diff) {
                if(!in_array(substr($key, 1), array_keys($modele))) {

                    continue;
                }

                if(in_array($key, array('/db2/num', '/db2/no_stock'))) {
                    
                    continue;
                }

                $tiers_array[$key] = sprintf("*%s (%s)", $tiers_array[$key], isset($tiers_array_old[$key]) ? $tiers_array_old[$key] : null);
                $nb_diff++;
            }

            if($nb_diff > 0) {
                $csv->add($this->makeLine($tiers_array, $modele));
            }
        }

        echo $csv->output();
    }

    protected function getOldDoc($tiers) {
        $doc = null;
        $id = $tiers->_id;

        if(isset($tiers->db2->import_revision)) {
            $doc = sfCouchdbManager::getClient()->rev($tiers->import_revision)->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON); 

            if($doc) {
                return $doc;
            }
        }

        $data_revs = sfCouchdbManager::getClient()->revs_info(true)->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
        $revs = $data_revs->_revs_info;
        $i = count($revs)-1;
        while(!$doc && $i >= 0) {
            $rev = $revs[$i]->rev;
            $doc = sfCouchdbManager::getClient()->rev($rev)->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            $i--;
            /*if(count($revs) - $i > 3) {

                break;
            }*/
        }

        if(!$doc) {
        
            return $tiers;
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

class sfCouchdbJsonNative
{
    
    protected $_stdclass = null;
    protected $_array = null;
    protected $_flat_array = null;

    public function __construct(stdClass $stdclass)
    {
        $this->_stdclass = $stdclass;
    }

    public function toStdClass() {
        return $this->_stdclass;
    }

    public function toArray() {
        if (is_null($this->_array)) {
            $this->_array = $this->stdClassToArray($this->_stdclass);
        }

        return $this->_array;
    }

    public function toFlatArray() {
        if (is_null($this->_flat_array)) {
            $this->_flat_array = $this->flattenArray($this->toArray());
        }

        return $this->_flat_array;
    }

    public function diff(sfCouchdbJsonNative $object) {

        return array_diff_assoc($this->toFlatArray(), $object->toFlatArray());
    }

    public function equal(sfCouchdbJsonNative $object) {

        return count($this->diff($object)) == 0 && count($object->diff($this)) == 0;
    }

    protected function stdClassToArray($stdclass) {

        return json_decode(json_encode($stdclass), true);
    }

    protected function flattenArray($array, $prefix = null, $decorator = "/")  {
        $flat_array = array();

        foreach($array as $key => $value) {
            if(is_array($value))  {
                $flat_array = array_merge($flat_array, $this->flattenArray($value, $prefix.$decorator.$key));
            } else {
                $flat_array[$prefix.$decorator.$key] = $value;
            }
        }

        return $flat_array;
    }
}
