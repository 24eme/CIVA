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
            new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'import from file', sfConfig::get('sf_data_dir') . '/import/Tiers-maj-20101123'),
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

	$insee = array();
        foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
	  $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
	  $insee[$csv[0]] = $csv[1];
	}

        $csv_no_stock = array();
        $csv_no = array();

        foreach (file($options['file']) as $a) {
	  $tiers = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $a)));
	  for($i = 0 ; $i < count($tiers) ; $i++) {
	    if (!isset($csv_no[$tiers[0]][$i]) || !$csv_no[$tiers[0]][$i])
	      $csv_no[$tiers[0]][$i] = $tiers[$i];
	    else if ($tiers[$i] && !$tiers[1]) {
	      $csv_no[$tiers[0]][$i] = $tiers[$i];
	    }
            
            if (!isset($csv_no_stock[$tiers[3]][$i]) || !$csv_no_stock[$tiers[3]][$i])
              $csv_no_stock[$tiers[3]][$i] = $tiers[$i];
            else if ($tiers[$i] && !$tiers[1]) {
              $csv_no_stock[$tiers[3]][$i] = $tiers[$i];
            }
            if ($tiers[1] && $i == 57)
	      $csv_no_stock[$tiers[3]][99] = $tiers[57];
	  }
	}

	foreach ($csv_no as $code_civa => $tiers) {
	  if (!$tiers[57])
	    continue;

          $tiers_stock = $csv_no_stock[$tiers[3]];

	  $json = new stdClass();
	  $json->type = "Tiers";
	  $json->_id = "TIERS-".$tiers[57];
	  $json->cvi = $tiers[57];
	  $json->num = $tiers[0];
	  $json->no_stock = $tiers[3];
	  $json->maison_mere = $tiers_stock[10];
	  $json->civaba = $tiers_stock[1];
	  $json->no_accises = $tiers_stock[70];
	  $json->siret = $tiers[58].'';
	  $json->intitule = $tiers_stock[9];
	  $json->regime_fiscal = '';
	  $json->nom = preg_replace('/ +/', ' ', $tiers[6]);
	  $json->declaration_insee = $tiers[62];
	  $json->declaration_commune = $insee[$tiers[62]];
	  $json->mot_de_passe = '{TEXT}0000';
	  $json->siege->adresse = $tiers[46];
	  $json->siege->insee_commune = $tiers[59];
	  $json->siege->code_postal = $tiers[60];
	  $json->siege->commune = $tiers[61];
	  if (isset($tiers_stock[99]))
	    $json->cvi_acheteur = $tiers_stock[99];
	  if ($tiers_stock[37])
	    $json->telephone = sprintf('%010d', $tiers_stock[37]);
	  if ($tiers_stock[39])
	    $json->fax = sprintf('%010d', $tiers_stock[39]);
	  if($tiers_stock[40])
	    $json->email = $tiers_stock[40];
	  if($tiers_stock[82])
	    $json->web = $tiers_stock[82];
	  $json->exploitant->sexe = $tiers_stock[41];
	  $json->exploitant->nom = $tiers_stock[42];
	  if ($tiers_stock[13]) {
	    $json->exploitant->adresse = $tiers_stock[12].", ".$tiers_stock[13];
	    $json->exploitant->code_postal = $tiers_stock[15];
	    $json->exploitant->commune = $tiers_stock[14];
	  }
	  if ($tiers_stock[25])
	    $json->exploitant->telephone = sprintf('%010d', $tiers_stock[38]);
	  $json->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers_stock[8], $tiers_stock[69], $tiers_stock[68]);

          if ($tiers[23] == "O") {
              $json->recoltant = 1;
          } else {
              $json->recoltant = 0;
          }

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
