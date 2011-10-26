<?php

class importConfiguration2011Task extends sfBaseTask {

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
        $this->name = 'Configuration2011';
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

	foreach (file(sfConfig::get('sf_data_dir') . '/import/10/Ceprec10') as $a) {
	  $csv = explode(',', preg_replace('/"/', '', $a));
	  $cepage_douane[$csv[1]][$csv[0]] = $csv[14];
	}
        
        $rendement_couleur_blanc_lieux_dits = 68;
        $rendement_couleur_rouge_lieux_dits = 60;
        
        $rendement_couleur_blanc_communale = 72;
        $rendement_couleur_rouge_communale = 60;

	$annee = "2011";

	$json = new stdClass();
        $json->_id = 'CONFIGURATION-'.$annee;
	$json->type = 'Configuration';
	$json->campagne = $annee;
        
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

        $lieu->couleur->cepage_CH = $this->getCepage('CH', $cepage_douane[1]['CH'], true);
        $lieu->couleur->cepage_SY = $this->getCepage('SY', $cepage_douane[1]['SY'], true);
        $lieu->couleur->cepage_AU = $this->getCepage('AU', $cepage_douane[1]['AU'], true);
        $lieu->couleur->cepage_PB = $this->getCepage('PB', $cepage_douane[1]['PB'], true);
        $lieu->couleur->cepage_PI = $this->getCepage('PI', $cepage_douane[1]['PI'], true);
        $lieu->couleur->cepage_ED = $this->getCepage('ED', $cepage_douane[1]['ED'], true);
        $lieu->couleur->cepage_RI = $this->getCepage('RI', $cepage_douane[1]['RI'], true);
        $lieu->couleur->cepage_PG = $this->getCepage('PG', $cepage_douane[1]['PG'], true);
        $lieu->couleur->cepage_MU = $this->getCepage('MU', $cepage_douane[1]['MU'], true);
        $lieu->couleur->cepage_MO = $this->getCepage('MO', $cepage_douane[1]['MO'], true);
        $lieu->couleur->cepage_GW = $this->getCepage('GW', $cepage_douane[1]['GW'], true);
        
	$json->recolte->appellation_ALSACEBLANC->lieu = $lieu;
        
        $json->recolte->appellation_LIEUDIT->appellation = "LIEUDIT";
        $json->recolte->appellation_LIEUDIT->libelle = "AOC Alsace Lieu-dit";
        $json->recolte->appellation_LIEUDIT->detail_lieu_editable = 1;
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_lieux_dits;
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->libelle = "Blanc";
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_CH = $this->getCepage('CH');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_SY = $this->getCepage('SY');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_AU = $this->getCepage('AU');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_PB = $this->getCepage('PB');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_PI = $this->getCepage('PI');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_ED = $this->getCepage('ED');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_RI = $this->getCepage('RI');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_PG = $this->getCepage('PG');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_MU = $this->getCepage('MU');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_MO = $this->getCepage('MO');
        $json->recolte->appellation_LIEUDIT->lieu->couleurBlanc->cepage_GW = $this->getCepage('GW');
        $json->recolte->appellation_LIEUDIT->lieu->couleurRouge->rendement_couleur = $rendement_couleur_rouge_lieux_dits;
        $json->recolte->appellation_LIEUDIT->lieu->couleurRouge->libelle = "Rouge";
        $json->recolte->appellation_LIEUDIT->lieu->couleurRouge->cepage_PR = $this->getCepage('PR');
        
        $appellation = new stdClass();
        
        $appellation->appellation = "COMMUNALE";
        $appellation->libelle = "AOC Alsace Communale";
        
        $appellation->lieuBLIE->libelle = "Blienschwiller";
        $appellation->lieuBLIE->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuBLIE->couleurBlanc->libelle = "Blanc";
        $appellation->lieuBLIE->couleurBlanc->cepage_SY = $this->getCepage('SY');
        
        $appellation->lieuBARR->libelle = "Côtes de Barr";
        $appellation->lieuBARR->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuBARR->couleurBlanc->libelle = "Blanc";
        $appellation->lieuBARR->couleurBlanc->cepage_SY = $this->getCepage('SY');
        
        $appellation->lieuROUF->libelle = "Côte de Rouffach";
        $appellation->lieuROUF->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuROUF->couleurBlanc->libelle = "Blanc";
        $appellation->lieuROUF->couleurBlanc->cepage_RI = $this->getCepage('RI');
        $appellation->lieuROUF->couleurBlanc->cepage_PG = $this->getCepage('PG');
        $appellation->lieuROUF->couleurBlanc->cepage_GW = $this->getCepage('GW');
        $appellation->lieuROUF->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->lieuROUF->couleurRouge->libelle = "Rouge";
        $appellation->lieuROUF->couleurRouge->cepage_PR = $this->getCepage('PR');
        
        $appellation->lieuKLEV->libelle = "Klevener de Heiligenstein";
        $appellation->lieuKLEV->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuKLEV->couleurBlanc->libelle = "Blanc";
        $appellation->lieuKLEV->couleurBlanc->cepage_KL->libelle = "Klevener";
        $appellation->lieuKLEV->couleurBlanc->cepage_KL->no_vtsgn = 1;
        
        $appellation->lieuOTTR->libelle = "Ottrott";
        $appellation->lieuOTTR->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->lieuOTTR->couleurRouge->libelle = "Rouge";
        $appellation->lieuOTTR->couleurRouge->cepage_PR = $this->getCepage('PR');
        
        $appellation->lieuRODE->libelle = "Rodern";
        $appellation->lieuRODE->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->lieuRODE->couleurRouge->libelle = "Rouge";
        $appellation->lieuRODE->couleurRouge->cepage_PR = $this->getCepage('PR');
        
        $appellation->lieuSTHI->libelle = "Saint Hippolyte";
        $appellation->lieuSTHI->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->lieuSTHI->couleurRouge->libelle = "Rouge";
        $appellation->lieuSTHI->couleurRouge->cepage_PR = $this->getCepage('PR');

        $appellation->lieuNOBL->libelle = "Vallée Noble";
        $appellation->lieuNOBL->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuNOBL->couleurBlanc->libelle = "Blanc";
        $appellation->lieuNOBL->couleurBlanc->cepage_RI = $this->getCepage('RI');
        $appellation->lieuNOBL->couleurBlanc->cepage_PG = $this->getCepage('PG');
        $appellation->lieuNOBL->couleurBlanc->cepage_GW = $this->getCepage('GW');
        
        $appellation->lieuSTGR->libelle = "Val Saint Grégoire";
        $appellation->lieuSTGR->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuSTGR->couleurBlanc->libelle = "Blanc";
        $appellation->lieuSTGR->couleurBlanc->cepage_AU = $this->getCepage('AU');
        $appellation->lieuSTGR->couleurBlanc->cepage_PB = $this->getCepage('PB');
        $appellation->lieuSTGR->couleurBlanc->cepage_PG = $this->getCepage('PG');
        
        $appellation->lieuSCHE->libelle = "Scherwiller";
        $appellation->lieuSCHE->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuSCHE->couleurBlanc->libelle = "Blanc";
        $appellation->lieuSCHE->couleurBlanc->cepage_RI = $this->getCepage('RI');
        
        $appellation->lieuWOLX->libelle = "Wolxheim";
        $appellation->lieuWOLX->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->lieuWOLX->couleurBlanc->libelle = "Blanc";
        $appellation->lieuWOLX->couleurBlanc->cepage_RI = $this->getCepage('RI');

        $json->recolte->appellation_COMMUNALE = $appellation;

        $json->recolte->appellation_PINOTNOIR->appellation = "PINOTNOIR";
	$json->recolte->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
	$json->recolte->appellation_PINOTNOIR->lieu->couleur->cepage_PN = $this->getCepage('PN');
	$json->recolte->appellation_PINOTNOIR->rendement_appellation = 75;
	$json->recolte->appellation_PINOTNOIR->douane->appellation_lieu = '001';
	$json->recolte->appellation_PINOTNOIR->douane->couleur = 'S';
	$json->recolte->appellation_PINOTNOIR->douane->code_cepage = '1';

        $json->recolte->appellation_PINOTNOIRROUGE->appellation = "PINOTNOIRROUGE";
	$json->recolte->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace PN rouge";
	$json->recolte->appellation_PINOTNOIRROUGE->rendement_appellation = 60;
	$json->recolte->appellation_PINOTNOIRROUGE->lieu->couleur->cepage_PR = $this->getCepage('PR');
	$json->recolte->appellation_PINOTNOIRROUGE->douane->appellation_lieu = '001';
	$json->recolte->appellation_PINOTNOIRROUGE->douane->couleur = 'R';
	$json->recolte->appellation_PINOTNOIRROUGE->douane->code_cepage = '1';

        $grdcru = new stdClass();
        $grdcru->appellation = "GRDCRU";
	$grdcru->libelle = "AOC Alsace Grand Cru";
	$grdcru->rendement = 61;
        $grdcru->douane->qualite = '';

	foreach(file(sfConfig::get('sf_data_dir') . '/import/11/Grdcrv11') as $l) {
	  $g = explode(',', preg_replace('/"/', '', $l));
	  if ($g[0] == $annee && !isset($g[1]) || $g[1] == "99")  {
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
	print_r($grdcru);

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
        $json->dr_non_ouverte = 1;
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
	return "Muscat";
      case 'ED':
	return 'Assemblage';
      case 'SY':
	return 'Sylvaner';
      default:
	echo "definition for $c missing\n";
	return ;
      }
    }
    
    public function getCepage($code, $code_depage = null, $rendement = null) {
        
        $cepages = new stdClass();
        
        $cepages->cepage_CH->libelle = "Chasselas";
        $cepages->cepage_CH->rendement = 100;
	$cepages->cepage_CH->douane->code_cepage = $code_depage;
        $cepages->cepage_CH->no_vtsgn = 1;
        
        $cepages->cepage_SY->libelle = "Sylvaner";
	$cepages->cepage_SY->rendement = 100;
	$cepages->cepage_SY->douane->code_cepage = $code_depage;
        $cepages->cepage_SY->no_vtsgn = 1;
        
        $cepages->cepage_AU->libelle = "Auxerrois";
	$cepages->cepage_AU->rendement = 100;
	$cepages->cepage_AU->douane->code_cepage = $code_depage;
        $cepages->cepage_AU->no_vtsgn = 1;
        
        $cepages->cepage_PB->libelle = "Pinot Blanc";
	$cepages->cepage_PB->rendement = 100;
	$cepages->cepage_PB->douane->code_cepage = $code_depage;
        $cepages->cepage_PB->no_vtsgn = 1;
        
        $cepages->cepage_PI->libelle = "Pinot";
	$cepages->cepage_PI->rendement = 100;
	$cepages->cepage_PI->douane->code_cepage = $code_depage;
        $cepages->cepage_PI->no_vtsgn = 1;

        $cepages->cepage_ED->libelle = "Assemblage";
	$cepages->cepage_ED->douane->code_cepage = $code_depage;
        $cepages->cepage_ED->superficie_optionnelle = 1;
        $cepages->cepage_ED->rendement = -1;
        $cepages->cepage_ED->no_vtsgn = 1;
        
	$cepages->cepage_RI->libelle = "Riesling";
	$cepages->cepage_RI->rendement = 90;
	$cepages->cepage_RI->douane->code_cepage = $code_depage;

	$cepages->cepage_PG->libelle = "Pinot Gris";
	$cepages->cepage_PG->rendement = 80;
	$cepages->cepage_PG->douane->code_cepage = $code_depage;

	$cepages->cepage_MU->libelle = "Muscat";
	$cepages->cepage_MU->rendement = 90;
	$cepages->cepage_MU->douane->code_cepage = $code_depage;
        
        $cepages->cepage_MO->libelle = "Muscat Ottonel";
	$cepages->cepage_MO->rendement = 90;
	$cepages->cepage_MO->douane->code_cepage = $code_depage;

	$cepages->cepage_GW->libelle = "Gewurzt.";
	$cepages->cepage_GW->rendement = 80;
	$cepages->cepage_GW->douane->code_cepage = $code_depage;
        
        $cepages->cepage_PN->libelle = "Pinot noir";
	$cepages->cepage_PN->no_vtsgn = 1;
        
        $cepages->cepage_PR->libelle = "Pinot noir rouge";
	$cepages->cepage_PR->no_vtsgn = 1;
        
        $code_entier = "cepage_".$code;
        
        if (isset($cepages->{$code_entier})) {
            $cepage = $cepages->{$code_entier};
            if (!$rendement && isset($cepage->rendement)) {
                $cepage->rendement = null;
            }
            if (!$code_depage && isset($code_depage->douane->code_cepage)) {
                $code_depage->douane->code_cepage = null;
            }
            return $cepage;
        } else {
            throw new sfCommandException("Cépage does not exist : ".$code);
        }
        
    }

}

