<?php

class importDRTask extends sfBaseTask {

    protected function configure() {
        // // add your own arguments here
        // $this->addArguments(array(
        //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
        // ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'stdout'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('limit', null, sfCommandOption::PARAMETER_REQUIRED, 'limit the number of imported record', -1),
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'insert data only for a given cvi', 0),
	));

        $this->namespace = 'import';
        $this->name = 'DR';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array()) {
        ini_set('memory_limit', '1024M');
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

        $achat = array();
        $achatcvi = array();
        /* Reconstitution des vendeurs pour chaque récoltant */
        foreach (file(sfConfig::get('sf_data_dir') . '/' . 'Dclven09') as $a) {
            $csv = explode(',', preg_replace('/"/', '', $a));
            $achat[$csv[0]][$csv[1]][$csv[4]][$csv[3]] = $csv[6];
            $achatcvi[$csv[0]][$csv[1]][$csv[4]][] = $csv[6];
        }

        $list_documents = array();
        $max = count(file(sfConfig::get('sf_data_dir') . '/' . "Dcllig09"));
        $nb = 0;
        foreach (file(sfConfig::get('sf_data_dir') . '/' . "Dcllig09") as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            $cvi = $csv[1];
	    if ($options['cvi'] && $cvi != $options['cvi'])
	      continue;
            $campagne = $csv[0];
            $_id = 'DR' . '-' . $cvi . '-' . $campagne;
            $appellation = $csv[3];
            $cepage = $csv[4];

            $doc = new stdClass();
            if (!isset($list_documents[$_id])) {
                $doc->type = 'DR';
                $doc->_id = $_id;
                $doc->cvi = $cvi;
                $doc->campagne = $campagne;
                $list_documents[$_id] = $doc;
            } else {
                $doc = $list_documents[$_id];
            }


            if (in_array($cepage, array('LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS'))) { /* Prise en compte des lies */

                if (is_null($doc->lies)) {
                    $doc->lies = 0;
                }
		$doc->lies += $this->recode_number($csv[12]);

            } elseif (in_array($cepage, array('AL', 'CR', 'GD', 'AN', 'LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS'))) {
	      if ($cepage == 'AN') {
		$doc->recolte->appellation_PINOTNOIR->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_PINOTNOIR->total_volume = $this->recode_number($csv[12]);
	      } else if ($cepage == 'CR') {
		$doc->recolte->appellation_CREMANT->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_CREMANT->total_volume = $this->recode_number($csv[12]);
	      }
            } else {

	        $detail = new stdClass();
                $detail->denomination = $csv[6];
		$detail->appellation = $this->convertappellation($appellation, $cepage);
                $detail->vtsgn = $csv[9];
                $detail->code_lieu = $csv[10];
                $detail->cepage = $cepage;
                $detail->superficie = $this->recode_number($csv[11]);
                $detail->volume = $this->recode_number($csv[12]);
                $detail->cave_particuliere = $this->recode_number($csv[21]);
                $detail->volume_revendique = $this->recode_number($csv[27]);
                $detail->volume_dplc = $this->recode_number($csv[28]);
                /* Les acheteurs */
                for ($i = 1; $i < 5; $i++) {
		  $val = $this->recode_number($csv[12 + $i]);
		  if ($val > 0) {
		    $acheteur = new stdClass();
		    $acheteur->cvi = $achat[$campagne][$cvi][$appellation][$i];
		    $acheteur->quantite_vendue = $val;
		    $detail->negoces[] = $acheteur;
		  }
                }
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
		      $cooperative = new stdClass();
		      $cooperative->cvi = $achat[$campagne][$cvi][$appellation][$i];
		      $cooperative->quantite_vendue = $val;
		      $detail->cooperatives[] = $cooperative;
                    }
                }
		$doc->recolte->{'appellation_'.$this->convertappellation($appellation, $cepage)}->appellation = $this->convertappellation($appellation, $cepage);
                $doc->recolte->{'appellation_'.$this->convertappellation($appellation, $cepage)}->{'lieu'.$csv[10]}->{"cepage_$cepage"}->detail[] = $detail;

            }
	    $nb++;
	    if ($options['import'] == 'couchdb')
	      $this->log($nb . '/' . $max);
            if ($options['limit'] > -1 && $nb >= $options['limit']) {
                break;
            }
        }

	//Ajout des totaux
        foreach (file(sfConfig::get('sf_data_dir') . '/' . "Dcltot09") as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            $cvi = $csv[1];
	    if ($options['civ'] && $cvi != $options['civ'])
	      continue;
            $campagne = $csv[0];
            $_id = 'DR' . '-' . $cvi . '-' . $campagne;
	    $appellation = $csv[3];
	    $cepage = $csv[4];
	    $appellation_new = $this->convertappellation($appellation, $cepage);
	    $lieu = $csv[10];
            if (!isset($list_documents[$_id])) {
	      continue;
            }
	    $doc = $list_documents[$_id];
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->total_superficie = $this->recode_number($csv[11]);
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->total_volume = $this->recode_number($csv[12]);
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->volume_revendique = $this->recode_number($csv[27]);
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->dplc = $this->recode_number($csv[28]);
	}

	//reconstruction des acheteurs
	foreach($list_documents as $doc) {
	  foreach ($doc->recolte as $nomappellation => $app) {
	    $acheteurs = array();
	    $coop = array();
	    foreach($app as $nom => $lieu) {
	      if ($lieu instanceOf stdClass)
		foreach($lieu as $nom => $cep) {
		  foreach ($cep->detail as $detail) {
		    if (isset($detail->negoces))
		      foreach ($detail->negoces as $a) {
			$acheteurs[$a->cvi] = $a->cvi;
		      }
		    if (isset($detail->cooperatives))
		      foreach ($detail->cooperatives as $c) {
			$coop[$c->cvi] = $c->cvi;
		      }
		  }
		}
	      $doc->acheteurs->{$nomappellation}->negoces = array_keys($acheteurs);
	      $doc->acheteurs->{$nomappellation}->cooperatives = array_keys($coop);
	    }
	  }
	}

	foreach (file(sfConfig::get('sf_data_dir') . '/' . "Dclent09") as $l) {
	  $csv = explode(',', preg_replace('/"/', '', $l));
	  $cvi = $csv[1];
	  if ($options['civ'] && $cvi != $options['civ'])
	    continue;
	  $campagne = $csv[0];
	  $_id = 'DR' . '-' . $cvi . '-' . $campagne;
	  if (!isset($list_documents[$_id])) {
	    continue;
	  }
	  $doc = $list_documents[$_id];
	  $doc->jeunes_vignes =  $this->recode_number($csv[6]);
	  $doc->lies =  $this->recode_number($csv[84]);

	  if ($this->recode_number($csv[91])) {
	    $doc->recolte->appellation_ALSACEBLANC->volume_revendique =  $this->recode_number($csv[91]);
	    $doc->recolte->appellation_ALSACEBLANC->dplc =  $this->recode_number($csv[92]);
	    $doc->acheteurs->appellation_ALSACEBLANC->cave_particuliere = ($this->recode_number($csv[10]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[95])) {	  
	    $doc->recolte->appellation_PINOTNOIR->volume_revendique =  $this->recode_number($csv[95]);
	    $doc->recolte->appellation_PINOTNOIR->dplc =  $this->recode_number($csv[96]);
	    $doc->acheteurs->appellation_PINOTNOIR->cave_particuliere = ($this->recode_number($csv[21]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[90])) {
	    $doc->recolte->appellation_PINOTROUGE->volume_revendique =  $this->recode_number($csv[99]);
	    $doc->recolte->appellation_PINOTROUGE->dplc =  $this->recode_number($csv[100]);
	    $doc->acheteurs->appellation_PINOTROUGE->cave_particuliere = ($this->recode_number($csv[32]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[103])) {
	    $doc->recolte->appellation_KLEVENER->volume_revendique =  $this->recode_number($csv[103]);
	    $doc->recolte->appellation_KLEVENER->dplc =  $this->recode_number($csv[104]);
	    $doc->acheteurs->appellation_KLEVENER->cave_particuliere = ($this->recode_number($csv[43]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[107])) {
	    $doc->recolte->appellation_CREMANT->volume_revendique =  $this->recode_number($csv[107]);
	    $doc->recolte->appellation_CREMANT->dplc =  $this->recode_number($csv[108]);
	    $doc->acheteurs->appellation_CREMANT->cave_particuliere = ($this->recode_number($csv[54]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[111])) {
	    $doc->recolte->appellation_GRDCRU->volume_revendique =  $this->recode_number($csv[111]);
	    $doc->recolte->appellation_GRDCRU->dplc =  $this->recode_number($csv[112]);
	    $doc->acheteurs->appellation_GRDCRU->cave_particuliere = ($this->recode_number($csv[65]) > 0) ? 1 : 0;
	  }
	}

	foreach (file(sfConfig::get('sf_data_dir') . '/' . "Dclgc09") as $l) {
	  $csv = explode(',', preg_replace('/"/', '', $l));
	  $cvi = $csv[1];
	  if ($options['civ'] && $cvi != $options['civ'])
	    continue;
	  $campagne = $csv[0];
	  $_id = 'DR' . '-' . $cvi . '-' . $campagne;
	  if (!isset($list_documents[$_id])) {
	    continue;
	  }
	  $doc = $list_documents[$_id];

	  if ($this->recode_number($csv[36])) {
	    $doc->recolte->appellation_GRDCRU->{'lieu'.$csv[3]}->volume_revendique =  $this->recode_number($csv[36]);
	    $doc->recolte->appellation_GRDCRU->{'lieu'.$csv[3]}->dplc =  $this->recode_number($csv[37]);
	  }
	  
	}

	if ($options['import'] == 'couchdb') {
	  foreach ($list_documents as $json) {
	    $doc = sfCouchdbManager::getClient()->createDocumentFromData($json);
	    $doc->save();
	  }
	  return ;
	}
	echo '{"docs":';
	echo json_encode(array_values($list_documents));
	echo '}';
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }
    private function convertappellation($appellation_db2, $cepage) {
      if ($appellation_db2 == 2)
	return 'CREMANT';
      if ($appellation_db2 == 3)
	return 'GRDCRU';
      if ($cepage == 'PN' || $cepage == 'AN')
	return 'PINOTNOIR';
      if ($cepage == 'PR')
	return 'PINOTROUGE';
      if ($cepage == 'KL') 
	return 'KLEVENER';
      if ($cepage == 'VT')
	return 'VINTABLE';
      return 'ALSACEBLANC';
    }

}
