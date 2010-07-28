<?php

class importTiersTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
	    new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'stdout'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
				));

        $this->namespace = 'import';
        $this->name = 'Tiers';
        $this->briefDescription = 'import csv tiers file';
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
	
	if($options['removedb'] && $options['import'] == 'couchdb') {
	  if (sfCouchdbManager::getClient()->databaseExists()) {
	    sfCouchdbManager::getClient()->deleteDatabase();
	  }
	  sfCouchdbManager::getClient()->createDatabase();
	}

	$docs = array();

        foreach (file(sfConfig::get('sf_data_dir') . '/' . 'Tiers09') as $a) {
	  $json = new stdClass();
	  $tiers = explode(',', preg_replace('/"/', '', $a));
	  if (!isset($tiers[4]) || !$tiers[4] || !strlen($tiers[4]))
	    continue;

	  $json->type = "Recoltant";
	  $json->_id = "REC-".$tiers[4];
	  $json->cvi = $tiers[4];
	  $json->num = $tiers[0];
	  $json->no_stock = $tiers[1];
	  $json->maison_mere = $tiers[2];
	  $json->civaba = $tiers[3];
	  $json->no_accises = $tiers[5];
	  $json->siret = $tiers[6];
	  $json->intitule = $tiers[7];
	  $json->nom = $tiers[8];
	  $json->siege->adresse = $tiers[9];
	  $json->siege->insee_commune = $tiers[10];
	  $json->siege->code_postal = $tiers[11];
	  $json->siege->commune = $tiers[12];
	  $json->telephone = sprintf('%010d', $tiers[14]);
	  if ($tiers[15]) 
	    $json->fax = sprintf('%010d', $tiers[15]);
	  if($tiers[16])
	    $json->email = $tiers[16];
	  if($tiers[17])
	    $json->web = $tiers[17];
	  $json->exploitant->sexe = $tiers[18];
	  $json->exploitant->nom = $tiers[19];
	  if ($tiers[21]) {
	    $json->exploitant->adresse = $tiers[20].", ".$tiers[21];
	    $json->exploitant->code_postal = $tiers[23];
	    $json->exploitant->commune = $tiers[24];
	  }
	  if ($tiers[25])
	    $json->exploitant->telephone = sprintf('%010d', $tiers[25]);
	  $json->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers[28], $tiers[27], $tiers[26]);
	  
	  $docs[] = $json;
	}
	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->createDocumentFromData($data);
	    $doc->save();
	  }
	  return ;
	}
	echo '{"docs":';
	echo json_encode($docs);
	echo '}';
    }
    
    private function recode_number($val) {
      return preg_replace('/^\./', '0.', $val) + 0;
    }
    
  }
