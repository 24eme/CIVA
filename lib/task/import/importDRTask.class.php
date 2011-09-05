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
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'Import data for the given year (09 by default)', '09'),
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
        foreach (file(sfConfig::get('sf_data_dir') . '/import/' . $options['year'].'/Dclven'.$options['year']) as $a) {
            $csv = explode(',', preg_replace('/"/', '', $a));
            $achat[$csv[0]][$csv[1]][$csv[4]][$csv[3]] = $csv[6];
            $achatcvi[$csv[0]][$csv[1]][$csv[4]][] = $csv[6];
        }


	/* Récupération des communes de déclaration */
	/*
	$commune = array();
	foreach (file(sfConfig::get('sf_data_dir').'/import/Commune') as $c) {
	  $csv = explode(',', preg_replace('/"/', '', $c));
	  $commune[$csv[0]]['commune'] = $csv[1];
	  $commune[$csv[0]]['sous_region'] = $csv[2];
	}
	*/
        $list_documents = array();
        $max = count(file(sfConfig::get('sf_data_dir') . '/import/' . $options['year']."/Dcllig".$options['year']));
        $nb = 0;
        foreach (file(sfConfig::get('sf_data_dir') . '/import/' . $options['year']."/Dcllig".$options['year']) as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            $cvi = $csv[1];
	    if ($options['cvi'] && $cvi != $options['cvi'])
	      continue;

            $campagne = $csv[0];
            $_id = 'DR' . '-' . $cvi . '-' . $campagne;
            $appellation = $csv[3];
            $cepage = $csv[4];
            $cepage_bis = $csv[5];

            $doc = new stdClass();
            if (!isset($list_documents[$_id])) {
                $doc->type = 'DR';
                $doc->_id = $_id;
                $doc->cvi = $cvi;
                $doc->campagne = $campagne;
                $doc->validee = $campagne.'-11-25';
                $doc->modifiee = $campagne.'-11-25';
                $doc->import_db2 = 1;
                $list_documents[$_id] = $doc;
            } else {
                $doc = $list_documents[$_id];
            }


            if (in_array($cepage, array('LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS', 'LK', 'LR'))) { /* Prise en compte des lies */

                if (!isset($doc->lies) || is_null($doc->lies)) {
                    $doc->lies = 0;
                }
		$doc->lies += $this->recode_number($csv[12]);

            } elseif (in_array($cepage, array('AL', 'CR', 'GD', 'AN', 'LA', 'LR', 'LN', 'LC', 'LM', 'LT', 'LG', 'LE', 'LS', 'AR', 'AK'))) {
	      if ($cepage == 'AN') {
		$doc->recolte->appellation_PINOTNOIR->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_PINOTNOIR->total_volume = $this->recode_number($csv[12]);
	      } else if ($cepage == 'CR') {
		$doc->recolte->appellation_CREMANT->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_CREMANT->total_volume = $this->recode_number($csv[12]);
	      }else if ($cepage == 'AR') {
		$doc->recolte->appellation_PINOTNOIRROUGE->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_PINOTNOIRROUGE->total_volume = $this->recode_number($csv[12]);
	      }else if ($cepage == 'AK') {
		$doc->recolte->appellation_KLEVENER->total_superficie = $this->recode_number($csv[11]);
		$doc->recolte->appellation_KLEVENER->total_volume = $this->recode_number($csv[12]);
	      }
            } else {

	        $detail = new stdClass();
                $detail->denomination = $csv[6];
		if ($campagne == 2007 && $cepage == 'PN' && $cepage_bis == 'RG') {
		  if (!$detail->denomination)
		    $detail->denomination = 'Rouge';
		  else if (!preg_match('/rouge/i', $detail->denomination)) {
		    $detail->denomination = 'Rouge - '.$csv[6];
		  }
		}
		$detail->appellation = $this->convertappellation($appellation, $cepage);
                $detail->vtsgn = $this->toVtSgn($csv[9]);
                $detail->code_lieu = $csv[10];
                $detail->cepage = $this->convertcepage($cepage, $cepage_bis);
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
		    $acheteur->cvi = $achat[$campagne][$cvi][$this->getNumeroAppellationVente($detail->appellation)][$i];
		    $acheteur->quantite_vendue = $val;
		    $detail->negoces[] = $acheteur;
		  }
                }
                /* les coopératives */
                for ($i = 5; $i < 8; $i++) {
                    $val = $this->recode_number($csv[13 + $i]);
                    if ($val > 0) {
		      $cooperative = new stdClass();
		      $cooperative->cvi = $achat[$campagne][$cvi][$this->getNumeroAppellationVente($detail->appellation)][$i];
		      $cooperative->quantite_vendue = $val;
		      $detail->cooperatives[] = $cooperative;
                    }
                }

		$doc->recolte->{'appellation_'.$this->convertappellation($appellation, $cepage)}->appellation = $this->convertappellation($appellation, $cepage);
                $doc->recolte->{'appellation_'.$this->convertappellation($appellation, $cepage)}->{'lieu'.$csv[10]}->{"cepage_".$this->convertcepage($cepage, $cepage_bis)}->detail[] = $detail;

            }
	    $nb++;
	    if ($options['import'] == 'couchdb')
	      $this->log($nb . '/' . $max);
            if ($options['limit'] > -1 && $nb >= $options['limit']) {
                break;
            }
        }

	//Ajout des totaux
        foreach (file(sfConfig::get('sf_data_dir') . '/import/' . $options['year'] ."/Dcltot".$options['year']) as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            $cvi = $csv[1];
	    if ($options['cvi'] && $cvi != $options['cvi'])
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
	    ->total_superficie = $this->recode_number($csv[32]); //total surface cepage calc
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->total_volume = $this->recode_number($csv[33]); //Total Vol cepage calc
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->volume_revendique = $this->recode_number($csv[37]); //Vol Revendiqué Cep Calc
	    $doc->recolte->{'appellation_'.$appellation_new}->{'lieu'.$lieu}->{'cepage_'.$cepage}
	    ->dplc = $this->recode_number($csv[38]); //Vol DPLC Cepage Calc
	}

        foreach ($list_documents as $doc) {
            //reconstruction des acheteurs
              foreach ($doc->recolte as $nomappellation => $app) {
                $acheteurs = array();
                $coop = array();
                $cave_particuliere = 0;
                foreach($app as $nom => $lieu) {
                  if ($lieu instanceOf stdClass) {
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
                        if (isset($detail->cave_particuliere) && $detail->cave_particuliere > 0) {
                            $cave_particuliere = 1;
                        }
                        if (!$detail->volume > 0) {
                            if ($nomappellation == 'appellation_ALSACEBLANC' &&
                               isset($doc->recolte->appellation_ALSACEBLANC->lieu->cepage_ED->total_volume) &&
                               $doc->recolte->appellation_ALSACEBLANC->lieu->cepage_ED->total_volume > 0) {
                               $detail->motif_non_recolte = "ae";
                            } else {
                               $detail->motif_non_recolte = "pc";
                            }
                        }
                      }
                    }
                  }
                  if (!count($acheteurs) > 0 && !count($coop) > 0) {
                    $cave_particuliere = 1;
                  }
                  $doc->acheteurs->{$nomappellation}->negoces = array_keys($acheteurs);
                  $doc->acheteurs->{$nomappellation}->cooperatives = array_keys($coop);
                  $doc->acheteurs->{$nomappellation}->cave_particuliere = $cave_particuliere;
                }
              }
        }

	foreach (file(sfConfig::get('sf_data_dir') . '/import/' . $options['year']. "/Dclent".$options['year']) as $l) {
	  $csv = explode(',', preg_replace('/"/', '', $l));
	  $cvi = $csv[1];
	  if ($options['cvi'] && $cvi != $options['cvi'])
	    continue;
	  $campagne = $csv[0];
	  $_id = 'DR' . '-' . $cvi . '-' . $campagne;
	  if (!isset($list_documents[$_id])) {
	    continue;
	  }
	  $doc = $list_documents[$_id];
	  $doc->jeunes_vignes =  $this->recode_number($csv[6]);
	  $doc->lies =  $this->recode_number($csv[84]);

	  $doc->declaration_insee = $csv[137];
	  $doc->declaration_commune = $csv[138];

	  if ($this->recode_number($csv[93])) {
	    $doc->recolte->appellation_ALSACEBLANC->lieu->volume_revendique =  $this->recode_number($csv[93]); //93
	    $doc->recolte->appellation_ALSACEBLANC->lieu->dplc =  $this->recode_number($csv[94]); //94
	    //$doc->acheteurs->appellation_ALSACEBLANC->cave_particuliere = ($this->recode_number($csv[10]) > 0) ? 1 : 0;
	  }
          if ($this->recode_number($csv[105])) {
	    $doc->recolte->appellation_KLEVENER->lieu->volume_revendique =  $this->recode_number($csv[105]); //105
	    $doc->recolte->appellation_KLEVENER->lieu->dplc =  $this->recode_number($csv[106]); //106
	    //$doc->acheteurs->appellation_KLEVENER->cave_particuliere = ($this->recode_number($csv[43]) > 0) ? 1 : 0;
	  }

	  if ($this->recode_number($csv[97])) {
	    $doc->recolte->appellation_PINOTNOIR->lieu->volume_revendique =  $this->recode_number($csv[97]); //97
	    $doc->recolte->appellation_PINOTNOIR->lieu->dplc =  $this->recode_number($csv[98]); //98
	    //$doc->acheteurs->appellation_PINOTNOIR->cave_particuliere = ($this->recode_number($csv[21]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[101])) {
	    $doc->recolte->appellation_PINOTNOIRROUGE->lieu->volume_revendique =  $this->recode_number($csv[101]); //101
	    $doc->recolte->appellation_PINOTNOIRROUGE->lieu->dplc =  $this->recode_number($csv[102]); //102
	    //$doc->acheteurs->appellation_PINOTNOIRROUGE->cave_particuliere = ($this->recode_number($csv[32]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[113])) {
	    $doc->recolte->appellation_GRDCRU->volume_revendique =  $this->recode_number($csv[113]); //113
	    $doc->recolte->appellation_GRDCRU->dplc =  $this->recode_number($csv[114]); //114
	    //$doc->acheteurs->appellation_GRDCRU->cave_particuliere = ($this->recode_number($csv[65]) > 0) ? 1 : 0;
	  }
	  if ($this->recode_number($csv[109])) {
	    $doc->recolte->appellation_CREMANT->lieu->volume_revendique =  $this->recode_number($csv[109]); //109
	    $doc->recolte->appellation_CREMANT->lieu->dplc =  $this->recode_number($csv[110]); //110
	    //$doc->acheteurs->appellation_CREMANT->cave_particuliere = ($this->recode_number($csv[54]) > 0) ? 1 : 0;
	  }
	}

	/*
	foreach (file(sfConfig::get('sf_data_dir') . '/import/' . "Dclgc09") as $l) {
	  $csv = explode(',', preg_replace('/"/', '', $l));
	  $cvi = $csv[1];
	  if ($options['cvi'] && $cvi != $options['cvi'])
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
	  
	  }*/

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

    private function toVtSgn($num) {
      switch($num) {
      case '1':
	return 'VT';
      case '2':
	return 'SGN';
      default:
	return '';
      }
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
	return 'PINOTNOIRROUGE';
      if ($cepage == 'KL') 
	return 'KLEVENER';
      if ($cepage == 'VT')
	return 'VINTABLE';
      return 'ALSACEBLANC';
    }

    private function getNumeroAppellationVente($appellation_nom) {
        if ($appellation_nom == 'ALSACEBLANC') {
            return 1;
        } elseif($appellation_nom == 'CREMANT') {
            return 2;
        } elseif($appellation_nom == 'GRDCRU') {
            return 3;
        } elseif($appellation_nom == 'PINOTNOIR') {
            return 4;
        } elseif($appellation_nom == 'PINOTNOIRROUGE') {
            return 6;
        } elseif($appellation_nom == 'KLEVENER') {
            return 5;
        } elseif($appellation_nom == 'VINTABLE') {
            return 9;
        } else {
            exit;
        }
    }

    private function convertcepage($cepage, $cepage_bis) {
      if ($cepage == 'VT' && in_array($cepage_bis, array('BL', 'RS', 'RG'))) {
          return $cepage_bis;
      } else {
          return $cepage;
      }
    }



}
