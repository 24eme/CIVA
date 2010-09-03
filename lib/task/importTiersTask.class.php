<?php

class importTiersTask extends sfBaseTask {

    protected function configure() {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
	    new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'import from file', sfConfig::get('sf_data_dir') . '/import/Tiers'),
	    new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year', '09'),

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
	
	if($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
	  if (sfCouchdbManager::getClient()->databaseExists()) {
	    sfCouchdbManager::getClient()->deleteDatabase();
	  }
	  sfCouchdbManager::getClient()->createDatabase();
	}

	$docs = array();
	$csv = array();
        foreach (file($options['file']) as $a) {
	  $tiers = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a)));
	  for($i = 0 ; $i < count($tiers) ; $i++) {
	    if (!isset($csv[$tiers[3]][$i]) || !$csv[$tiers[3]][$i])
	      $csv[$tiers[3]][$i] = $tiers[$i];
	    else if ($tiers[$i] && !$tiers[1]) {
	      $csv[$tiers[3]][$i] = $tiers[$i];
	    }
	    if ($tiers[1] && $i == 57)
	      $csv[$tiers[3]][99] = $tiers[57];
	  }
	}

	foreach ($csv as $code_civa => $tiers) {
	  if (!$tiers[57])
	    continue;

	  $json = new stdClass();
	  $json->type = "Tiers";
	  $json->_id = "TIERS-".$tiers[57];
	  $json->cvi = $tiers[57];
	  $json->num = $tiers[0];
	  $json->no_stock = $tiers[3];
	  $json->maison_mere = $tiers[10];
	  $json->civaba = $tiers[1];
	  $json->no_accises = $tiers[70];
	  $json->siret = $tiers[58].'';
	  $json->intitule = $tiers[9];
	  $json->regime_fiscal = '';
	  $json->nom = preg_replace('/ +/', ' ', $tiers[6]);
	  $json->insee_commune_declaration = $tiers[62];
	  $json->mot_de_passe = '{TEXT}0000';
	  $json->siege->adresse = $tiers[46];
	  $json->siege->insee_commune = $tiers[59];
	  $json->siege->code_postal = $tiers[60];
	  $json->siege->commune = $tiers[61];
	  if (isset($tiers[99]))
	    $json->cvi_acheteur = $tiers[99];
	  if ($tiers[37]) 
	    $json->telephone = sprintf('%010d', $tiers[37]);
	  if ($tiers[39]) 
	    $json->fax = sprintf('%010d', $tiers[39]);
	  if($tiers[40])
	    $json->email = $tiers[40];
	  if($tiers[82])
	    $json->web = $tiers[82];
	  $json->exploitant->sexe = $tiers[41];
	  $json->exploitant->nom = $tiers[42];
	  if ($tiers[13]) {
	    $json->exploitant->adresse = $tiers[12].", ".$tiers[13];
	    $json->exploitant->code_postal = $tiers[15];
	    $json->exploitant->commune = $tiers[14];
	  }
	  if ($tiers[25])
	    $json->exploitant->telephone = sprintf('%010d', $tiers[38]);
	  $json->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers[8], $tiers[68], $tiers[69]);
	  
	  $docs[] = $json;
	}

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->retrieveDocumentById($data->_id);
	    if ($doc) {
	      $doc->delete();
	    }
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
