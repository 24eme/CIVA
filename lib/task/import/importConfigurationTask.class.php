<?php

class importConfigurationTask extends sfBaseTask {

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
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'couchdb'),
            new sfCommandOption('removedb', null, sfCommandOption::PARAMETER_REQUIRED, '= yes if remove the db debore import [yes|no]', 'no'),
            new sfCommandOption('year', null, sfCommandOption::PARAMETER_REQUIRED, 'year version of the file to be imported', '09'),
        ));

        $this->namespace = 'import';
        $this->name = 'Configuration';
        $this->briefDescription = 'import csv achat file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
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

	foreach (file(sfConfig::get('sf_data_dir') . '/import/Ceprec') as $a) {
	  $csv = explode(',', preg_replace('/"/', '', $a));
	  $cepage_douane[$csv[1]][$csv[0]] = $csv[14];
	}

	$annee = "2010";

	$docs = array();
	$json = new stdClass();
	$json->_id = 'CONFIGURATION';
	$json->type = 'Configuration';
	$json->delete = 1;
	$docs[] = $json;

	$json = new stdClass();
	$json->_id = 'CONFIGURATION-2009';
	$json->type = 'Configuration';
	$json->campagne = '2009';
	$json->virtual = "2010";
	$docs[] = $json;

	$json = new stdClass();
	$json->_id = 'CONFIGURATION-2008';
	$json->type = 'Configuration';
	$json->campagne = '2008';
	$json->virtual = "2010";
	$docs[] = $json;

	$json = new stdClass();
	$json->_id = 'CONFIGURATION-2007';
	$json->type = 'Configuration';
	$json->campagne = '2007';
	$json->virtual = "2010";
	$docs[] = $json;

	$json = new stdClass();
        $json->recolte->douane->appellation_lieu = '001';
        $json->recolte->douane->type_aoc = '1';
        $json->recolte->douane->couleur = 'B';
        $json->recolte->douane->qualite = 'S ';
        $json->recolte->douane->qualite_vt = 'D7';
        $json->recolte->douane->qualite_sgn = 'D6';

        $json->recolte->appellation_ALSACEBLANC->appellation = "ALSACEBLANC";
	$json->recolte->appellation_ALSACEBLANC->libelle = "AOC Alsace blanc";
        $json->recolte->appellation_ALSACEBLANC->rendement_appellation = 80;
        $json->recolte->appellation_ALSACEBLANC->douane->qualite = '';
        
	$lieu = new stdClass();
        $lieu->douane->qualite = 'S ';

        $lieu->couleur->cepage_CH->libelle = "Chasselas";
	$lieu->couleur->cepage_CH->rendement = 100;
	$lieu->couleur->cepage_CH->douane->code_cepage = $cepage_douane[1]['CH'];
        $lieu->couleur->cepage_CH->no_vtsgn = 1;

	$lieu->couleur->cepage_SY->libelle = "Sylvaner";
	$lieu->couleur->cepage_SY->rendement = 100;
	$lieu->couleur->cepage_SY->douane->code_cepage = $cepage_douane[1]['SY'];
        $lieu->couleur->cepage_SY->no_vtsgn = 1;

        $lieu->couleur->cepage_PB->libelle = "Pinot Blanc";
	$lieu->couleur->cepage_PB->rendement = 100;
	$lieu->couleur->cepage_PB->douane->code_cepage = $cepage_douane[1]['PB'];
        $lieu->couleur->cepage_PB->no_vtsgn = 1;

        $lieu->couleur->cepage_ED->libelle = "Edelzwicker";
	$lieu->couleur->cepage_ED->douane->code_cepage = $cepage_douane[1]['ED'];
        $lieu->couleur->cepage_ED->superficie_optionnelle = 1;
        $lieu->couleur->cepage_ED->rendement = -1;
        $lieu->couleur->cepage_ED->no_vtsgn = 1;
        
	$lieu->couleur->cepage_RI->libelle = "Riesling";
	$lieu->couleur->cepage_RI->rendement = 90;
	$lieu->couleur->cepage_RI->douane->code_cepage = $cepage_douane[1]['RI'];

	$lieu->couleur->cepage_PG->libelle = "Pinot Gris";
	$lieu->couleur->cepage_PG->rendement = 80;
	$lieu->couleur->cepage_PG->douane->code_cepage = $cepage_douane[1]['PG'];

	$lieu->couleur->cepage_MU->libelle = "Muscat d'Alsace";
	$lieu->couleur->cepage_MU->rendement = 90;
	$lieu->couleur->cepage_MU->douane->code_cepage = $cepage_douane[1]['MU'];

	$lieu->couleur->cepage_GW->libelle = "Gewurzt.";
	$lieu->couleur->cepage_GW->rendement = 80;
	$lieu->couleur->cepage_GW->douane->code_cepage = $cepage_douane[1]['GW'];

	$json->recolte->appellation_ALSACEBLANC->lieu = $lieu;

        $json->recolte->appellation_KLEVENER->appellation = "KLEVENER";
	$json->recolte->appellation_KLEVENER->libelle = "AOC Klevener de Heiligenstein";
	$json->recolte->appellation_KLEVENER->rendement_appellation = 75;
        $json->recolte->appellation_KLEVENER->no_total_cepage = 1;
	$json->recolte->appellation_KLEVENER->lieu->couleur->cepage_KL->libelle = "Klevener";
	$json->recolte->appellation_KLEVENER->lieu->couleur->cepage_KL->no_vtsgn = 1;
	$json->recolte->appellation_KLEVENER->douane->appellation_lieu = '054';
	$json->recolte->appellation_KLEVENER->douane->code_cepage = '';
        $json->recolte->appellation_KLEVENER->douane->qualite = 'S';
        
	$json->_id = 'CONFIGURATION-'.$annee;
	$json->type = 'Configuration';
	$json->campagne = $annee;

        $json->recolte->appellation_PINOTNOIR->appellation = "PINOTNOIR";
	$json->recolte->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
	$json->recolte->appellation_PINOTNOIR->lieu->couleur->cepage_PN->libelle = "Pinot noir";
	$json->recolte->appellation_PINOTNOIR->lieu->couleur->cepage_PN->no_vtsgn = 1;
	$json->recolte->appellation_PINOTNOIR->rendement_appellation = 75;
	$json->recolte->appellation_PINOTNOIR->douane->appellation_lieu = '001';
	$json->recolte->appellation_PINOTNOIR->douane->couleur = 'S';
	$json->recolte->appellation_PINOTNOIR->douane->code_cepage = '1';

        $json->recolte->appellation_PINOTNOIRROUGE->appellation = "PINOTNOIRROUGE";
	$json->recolte->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace Pinot noir rouge";
	$json->recolte->appellation_PINOTNOIRROUGE->rendement_appellation = 60;
	$json->recolte->appellation_PINOTNOIRROUGE->lieu->couleur->cepage_PR->libelle = "Pinot noir";
	$json->recolte->appellation_PINOTNOIRROUGE->lieu->couleur->cepage_PR->no_vtsgn = 1;
	$json->recolte->appellation_PINOTNOIRROUGE->douane->appellation_lieu = '001';
	$json->recolte->appellation_PINOTNOIRROUGE->douane->couleur = 'R';
	$json->recolte->appellation_PINOTNOIRROUGE->douane->code_cepage = '1';

        $grdcru = new stdClass();
        $grdcru->appellation = "GRDCRU";
	$grdcru->libelle = "AOC Alsace Grand Cru";
	$grdcru->rendement = 61;
        $grdcru->douane->qualite = '';

	foreach(file(sfConfig::get('sf_data_dir') . '/import/10/Grdcrv10') as $l) {
	  $g = explode(',', preg_replace('/"/', '', $l));
                
	  if (!isset($g[1]) || $g[1] == "99")  {
	    continue;
	  }
	  if (!$g[2]) {
	    $grdcru->{'lieu'.$g[1]}->libelle = $g[3];
	    $r = $this->recode_number($g[4]) + $this->recode_number($g[5]);
	    //	    echo $g[3]." rendement: $r\n";
	    if ($r != $grdcru->rendement)
	      $grdcru->{'lieu'.$g[1]}->rendement = $r;
	  }else{
	    if (preg_match('/^L/', $g[2]))
	      continue;
	    $grdcru->{'lieu'.$g[1]}->couleur->{'cepage_'.$g[2]}->libelle = $this->convertCepage2Libelle($g[2]);
	    $grdcru->{'lieu'.$g[1]}->couleur->{'cepage_'.$g[2]}->douane->code_cepage = $cepage_douane[3][$g[2]];
            $grdcru->{'lieu'.$g[1]}->couleur->{'cepage_'.$g[2]}->douane->qualite = 'S ';
	    $grdcru->{'lieu'.$g[1]}->douane->appellation_lieu = $g[7];
	    if (isset($grdcru->{'lieu'.$g[1]}->rendement) && $grdcru->{'lieu'.$g[1]}->rendement != $g[4])
	      $grdcru->{'lieu'.$g[1]}->couleur->{'cepage_'.$g[2]}->rendement = $this->recode_number($g[4]) + $this->recode_number($g[5]);
	    if ($g[2] == 'ED' || $g[2] == 'SY')
	      $grdcru->{'lieu'.$g[1]}->couleur->{'cepage_'.$g[2]}->no_vtsgn = 1;
	      
  	  }
	}
	$json->recolte->appellation_GRDCRU = $grdcru;

        $json->recolte->appellation_CREMANT->appellation = "CREMANT";
	$json->recolte->appellation_CREMANT->libelle = "AOC Crémant d'Alsace";
	$json->recolte->appellation_CREMANT->douane->qualite = 'M';

        $json->recolte->appellation_CREMANT->lieu->douane->qualite = 'M0';

	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_PB->libelle = "Pinot Blanc";
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PB->no_vtsgn = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PB->douane->code_cepage = $cepage_douane[2]['PB'];

        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_CD->libelle = "Chardonnay";
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_CD->no_vtsgn = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_CD->douane->code_cepage = $cepage_douane[2]['CD'];

	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_BN->libelle = "Pinot Noir Blanc";
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_BN->no_vtsgn = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_BN->douane->code_cepage = $cepage_douane[2]['BN'];

	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RI->libelle = "Riesling";
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RI->no_vtsgn = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RI->douane->code_cepage = $cepage_douane[2]['RI'];

	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_PG->libelle = "Pinot Gris";
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PG->no_vtsgn = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PG->douane->code_cepage = $cepage_douane[2]['PG'];

	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->libelle = "Pinot Noir Rosé";
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->douane->couleur = 'S';
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->douane->qualite = 'M ';
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->douane->code_cepage = '1';
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->no_vtsgn = 1;
        //$json->recolte->appellation_CREMANT->lieu->couleur->cepage_PN->douane->code_cepage = $cepage_douane[2]['PN'];



	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->libelle = "Rebêches";
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->rendement = -1;
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->min_quantite = 0.02;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->max_quantite = 0.1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->no_negociant = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->no_mout = 1;
        $json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->no_motif_non_recolte = 1;
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->exclude_total = 1;
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->douane->type_aoc = 4;
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->douane->appellation_lieu = '999';
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->douane->qualite = 'B';
	$json->recolte->appellation_CREMANT->lieu->couleur->cepage_RB->douane->code_cepage = '';

	$json->recolte->appellation_CREMANT->rendement_appellation = 80;
        $json->recolte->appellation_CREMANT->mout = 1;
	$json->recolte->appellation_CREMANT->douane->appellation_lieu = '001';

        $json->recolte->appellation_VINTABLE->appellation = "VINTABLE";
        $json->recolte->appellation_VINTABLE->exclude_total = 1;
        $json->recolte->appellation_VINTABLE->no_total_cepage = 1;
	$json->recolte->appellation_VINTABLE->libelle = "Vins sans IG";
	$json->recolte->appellation_VINTABLE->douane->type_aoc = 4;
	$json->recolte->appellation_VINTABLE->douane->appellation_lieu = 999;
	$json->recolte->appellation_VINTABLE->douane->qualite_aoc = '';
        $json->recolte->appellation_VINTABLE->douane->qualite = '';
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_BL->libelle = "Blanc";
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_BL->douane->couleur = "B";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_BL->douane->code_cepage = "";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_BL->no_vtsgn = 1;
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RS->libelle = "Rosé";
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RS->douane->couleur = "S";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RS->douane->code_cepage = "";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RS->no_vtsgn = 1;
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RG->libelle = "Rouge";
	$json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RG->douane->couleur = "R";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RG->douane->code_cepage = "";
        $json->recolte->appellation_VINTABLE->lieu->couleur->cepage_RG->no_vtsgn = 1;
        $json->recolte->appellation_VINTABLE->rendement = -1;
        $json->recolte->appellation_VINTABLE->rendement_appellation = -1;


	$json->intitule = array("CAVES", "DOMAINE", "EAR", "EARL", "EURL", "GAEC", "GFA, DU", "HERITIERS", "INDIVISION", "M.", "MADAME", "MADEME", "MAISON", "MELLE", "M., ET, MME", "MLLE", "MM.", "MME", "MMES", "MME, VEUVE", "MRS", "S.A.", "SA", "SARL", "S.A.S.", "SAS", "SASU", "S.C.A.", "SCA", "SCEA", "S.C.I.", "SCI", "S.D.F.", "SDF", "SICA", "STE", "STEF", "VEUVE", "VINS");

        $json->motif_non_recolte = array('AE' => "Assemblage Edelzwicker", 'DC' => "Déclaration en cours", 'PC' => "Problème climatique", 'MV' => "Maladie de la vigne", 'MP' => "Motifs personnels", 'VV' => "Vendanges en Vert");
        
	$docs[] = $json;

	$json = new stdClass();
	$json->_id = 'CURRENT';
	$json->type = 'Current';
	$json->campagne = $annee;
	$json->dr_non_editable = 0;
	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->retrieveDocumentById($data->_id);
	    if ($doc) {
	      $doc->delete();
	    }
	    if (isset($data->delete))
	      continue;
            $doc = sfCouchdbManager::getClient()->createDocumentFromData($data);
	    $doc->save();
	  }
	  return;
	}
	echo '{"docs":';
	echo json_encode($docs);
	echo '}';
    }

    private function recode_number($val) {
        return preg_replace('/^\./', '0.', $val) + 0;
    }
    private function convertCepage2Libelle($c) {
      switch ($c) {
      case 'RI':
	return 'Riesling';
      case 'GW':
	return 'Gewurzt.';
      case 'PG':
	return 'Pinot Gris';
      case 'MU':
	return "Muscat d'Alsace";
      case 'ED':
	return 'Assemblage';
      case 'SY':
	return 'Sylvaner';
      default:
	echo "definition for $c missing\n";
	return ;
      }
    }

}

