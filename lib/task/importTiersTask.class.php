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
            new sfCommandOption('file', null, sfCommandOption::PARAMETER_REQUIRED, 'import from file', sfConfig::get('sf_data_dir') . '/import/Tiers-2010-2011'),
	    new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year', '09'),

				));

        $this->namespace = 'import';
        $this->name = 'Tiers';
        $this->briefDescription = 'import csv tiers file';
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '512M');

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
	
	if($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
	  if (sfCouchdbManager::getClient()->databaseExists()) {
	    sfCouchdbManager::getClient()->deleteDatabase();
	  }
	  sfCouchdbManager::getClient()->createDatabase();
	}

        $nb_modified = 0;
        $nb_add = 0;

	$docs = array();
	$csv = array();

	$insee = array();
        foreach (file(sfConfig::get('sf_data_dir') . '/import/Commune') as $c) {
	  $csv = explode(',', preg_replace('/"/', '', preg_replace('/"\W+$/', '"', $c)));
	  $insee[$csv[0]] = $csv[1];
	}

        $csv_no_stock = array();
        $csv_no = array();

        $this->logSection('use file', $options['file']);
        
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
          if ($tiers[1] && !$tiers[57]) {
            $csv_no_stock[$tiers[3]]['no_metteur_marche'] = $tiers[0];
          }
	}

        $index_keep_tiers_stock = array(10, 1, 9, 99, 37, 39, 40, 82, 41, 42, 12, 13, 14, 15, 38, 8 ,69, 68);

	foreach ($csv_no as $code_civa => $tiers) {
	  if (!$tiers[57])
	    continue;

          $tiers_stock = $csv_no_stock[$tiers[3]];
          $tiers_metteur_marche = null;

          if (isset($tiers_stock['no_metteur_marche']) && isset($csv_no[$tiers_stock['no_metteur_marche']])) {
              $tiers_metteur_marche = $csv_no[$tiers_stock['no_metteur_marche']];
          }

          if (!$tiers[1]) {
              foreach($index_keep_tiers_stock as $index) {
                  if (isset($tiers_stock[$index])) {
                    $tiers[$index] = $tiers_stock[$index];
                  }
              }
              if ($tiers_metteur_marche && isset($tiers_stock[70])) {
                  $tiers[70] = $tiers_stock[70];
              }
          }



          $tiers_object = sfCouchdbManager::getClient('Tiers')->retrieveByCvi($tiers[57]);

          if (!$tiers_object) {
              $tiers_object = new Tiers();
              $tiers_object->set('_id', "TIERS-".$tiers[57]);
              $tiers_object->cvi = $tiers[57];
              $tiers_object->mot_de_passe = '{NOUVEAU}';
              if($tiers[40])
                $tiers_object->email = $tiers[40];
          }

          $tiers_object->num = $tiers[0];
          $tiers_object->no_stock = $tiers[3];
          $tiers_object->maison_mere = $tiers[10];
          $tiers_object->civaba = $tiers[1];
          $tiers_object->no_accises = $tiers[70];
          $tiers_object->siret = $tiers[58].'';
          $tiers_object->intitule = $tiers[9];
          $tiers_object->regime_fiscal = '';
          $tiers_object->nom = preg_replace('/ +/', ' ', $tiers[6]);
          $tiers_object->declaration_insee = $tiers[62];
          $tiers_object->declaration_commune = $insee[$tiers[62]];
          $tiers_object->siege->adresse = $tiers[46];
          $tiers_object->siege->insee_commune = $tiers[59];
          $tiers_object->siege->code_postal = $tiers[60];
          $tiers_object->siege->commune = $tiers[61];
          if (isset($tiers[99]))
            $tiers_object->cvi_acheteur = $tiers[99];
          if ($tiers[37])
            $tiers_object->telephone = sprintf('%010d', $tiers[37]);
          if ($tiers[39])
            $tiers_object->fax = sprintf('%010d', $tiers[39]);
          if(isset($tiers[82]) && $tiers[82]) {
            $tiers_object->web = $tiers[82];
          }
          $tiers_object->exploitant->sexe = $tiers[41];
          if ($tiers[42]) {
            $tiers_object->exploitant->nom = $tiers[42];
          } else {
            $tiers_object->exploitant->nom = $tiers_object->nom;
          }
          if ($tiers[13]) {
            $tiers_object->exploitant->adresse = $tiers[12].", ".$tiers[13];
            $tiers_object->exploitant->code_postal = $tiers[15];
            $tiers_object->exploitant->commune = $tiers[14];
          } else {
            $tiers_object->exploitant->adresse = $tiers_object->siege->adresse;
            $tiers_object->exploitant->code_postal = $tiers_object->siege->code_postal;
            $tiers_object->exploitant->commune = $tiers_object->siege->commune;
          }
          if ($tiers[38]) {
            $tiers_object->exploitant->telephone = sprintf('%010d', $tiers[38]);
          } else {
            $tiers_object->exploitant->telephone = $tiers_object->telephone;
          }
          $tiers_object->exploitant->date_naissance = sprintf("%04d-%02d-%02d", $tiers[8], $tiers[69], $tiers[68]);

          if ($tiers[23] == "O") {
              $tiers_object->recoltant = 1;
          } else {
              $tiers_object->recoltant = 0;
          }

          if ($tiers_metteur_marche) {
            $tiers_object->add('metteur_marche')->nom = preg_replace('/ +/', ' ', $tiers_metteur_marche[6]);
          }

          if ($tiers_object->isNew()) {
              $nb_add++;
              $this->logSection($tiers_object->cvi, 'added');
          } else {
              $nb_modified++;
              $this->logSection($tiers_object->cvi, 'updated');
          }

          $tiers_object->save();
          
          unset($tiers_object);
	}

        $this->logSection("added", $nb_add);
        $this->logSection("updated", $nb_modified);

	/*if ($options['import'] == 'couchdb') {
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
	echo '}';*/
    }
    
    private function recode_number($val) {
      return preg_replace('/^\./', '0.', $val) + 0;
    }

    private function generatePass() {
        return sprintf("{TEXT}%04d", rand(0, 9999));
    }
    
  }
