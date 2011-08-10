<?php

class compteUpdateTask extends sfBaseTask {

    protected $_db2 = array();
    
    protected function configure() {
        $this->addArguments(array(
            //new sfCommandArgument('file', sfCommandArgument::REQUIRED, 'My import from file'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'compte';
        $this->name = 'update';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [importCompte|INFO] task does things.
Call it with:

  [php symfony importCompte|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $ids_compte = sfCouchdbManager::getClient("_Compte")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        $ids_tiers = array_merge(sfCouchdbManager::getClient("Recoltant")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds(), sfCouchdbManager::getClient("MetteurEnMarche")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds());

        $this->logSection("nb compte", count($ids_compte));
        $this->logSection("nb tiers", count($ids_tiers));

        $stocks = array();
        $db2 = array();

        foreach ($ids_tiers as $id) {
            $tiers_json = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            if (!array_key_exists($tiers_json->db2->no_stock, $stocks)) {
                $stocks[$tiers_json->db2->no_stock] = array("tiers" => array(), "compte" => null);
            }
            $stocks[$tiers_json->db2->no_stock]["tiers"][$tiers_json->db2->num] = $id;
        }

        foreach ($ids_compte as $id) {
            $compte_json = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            if ($compte_json->type == "CompteTiers") {
                if (!array_key_exists($compte_json->db2->no_stock, $stocks)) {
                    $stocks[$compte_json->db2->no_stock] = array("tiers" => array(), "compte" => null);
                }
                $stocks[$compte_json->db2->no_stock]["compte"] = $id;
            }
        }

        $this->logSection("nb stock", count($stocks));

        /*foreach (file($arguments['file']) as $a) {
            $db2_tiers = new Db2Tiers(explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a))));
            $this->_db2[$db2_tiers->get(Db2Tiers::COL_NUM)] = $db2_tiers;
        }*/

        foreach ($stocks as $no_stock => $stock) {
            $tiers_json = array();
            foreach($stock['tiers'] as $num => $id) {
                $tiers_json[$id] = sfCouchdbManager::getClient()->retrieveDocumentById($id, sfCouchdbClient::HYDRATE_JSON);
            }
	    $login = $this->getLogin($tiers_json);
	    $compte = $this->getCompteOrGenerateIt($stock['compte'], $login, $no_stock);
	    $compte->email = $this->combiner($tiers_json, 'email');
            $compte->remove("tiers");
            $compte->add("tiers");
            
            foreach($tiers_json as $tiers) {
                $obj = $compte->tiers->add($tiers->_id);
                $obj->id = $tiers->_id;
                $obj->type = $tiers->type;
                $obj->nom = $tiers->nom;
                if ($tiers->compte != $compte->get('_id')) {
                    $tiers_obj = sfCouchdbManager::getClient()->retrieveDocumentById($tiers->_id);  
                    $tiers_obj->compte = $compte->get('_id');
                    $tiers_obj->save();
                }
            }
           
            $compte->save();
            $this->logSection("saved",$compte->get('_id'));
        }

	foreach(sfCouchdbManager::getClient("Acheteur")->getAll(sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds() as $id) {
	  $tiers = sfCouchdbManager::getClient()->retrieveDocumentById($id);  
	  $login = preg_replace('/ACHAT-/', '', $id);
	  $compte = $this->getCompteOrGenerateIt('COMPTE-'.$login, $login);
	  $obj = $compte->tiers->add($tiers->_id);
	  $obj->id = $tiers->_id;
	  $obj->type = $tiers->type;
	  $obj->nom = $tiers->nom;
	  $compte->save();
	  $tiers->compte = $compte->get('_id');
	  $tiers->save();
	  $this->logSection("acheteur saved",$compte->get('_id'));
	}
    }

    private function getCompteOrGenerateIt($compteid, $login, $no_stock = null) {
      $compte = ($compteid) ? sfCouchdbManager::getClient()->retrieveDocumentById($compteid) : null;
      if (!$compte) {
	$compte = new CompteTiers();
	$compte->set('_id', 'COMPTE-'.$login);
                $compte->login = $login;
                $compte->mot_de_passe = $this->generatePass();
		if ($no_stock) 
		  $compte->db2->no_stock = $no_stock;
      }
      return $compte;
    }

    private function getLogin($tiers_json) {
        $login = null;
        foreach($tiers_json as $tiers) {
          if ($tiers->type == 'Recoltant' && $tiers->cvi) {
              return $tiers->cvi;
          } elseif(is_null($login) && $tiers->civaba) {
              $login = 'C'.$tiers->civaba;
          }
        }
        return $login;
    }
    
    private function combiner($tiers_json, $field) {
        $value = null;
        foreach($tiers_json as $tiers) {
          if ($tiers->type == 'Recoltant' && $tiers->$field) {
              return $tiers->$field;
          } elseif(is_null($value) && $tiers->$field) {
              $value = $tiers->$field;
          }
        }
        
        return $value;
    }

    private function generatePass() {
        return sprintf("{TEXT}%04d", rand(0, 9999));
    }
}
