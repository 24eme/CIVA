<?php

class importConfigurationTask extends sfBaseTask {

    protected $cepage_order = array("CH", "SY", "AU", "PB", "PI", "ED", "RI", "PG", "MU", "MO", "GW");
    
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
	  if (acCouchdbManager::getClient()->databaseExists()) {
	    acCouchdbManager::getClient()->deleteDatabase();
	  }
	  acCouchdbManager::getClient()->createDatabase();
	}

	foreach (file(sfConfig::get('sf_data_dir') . '/import/10/Ceprec10') as $a) {
	  $csv = explode(',', preg_replace('/"/', '', $a));
	  $cepage_douane[$csv[1]][$csv[0]] = $csv[14];
	}

	$annee = "2010";

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

    $json->_id = 'CONFIGURATION-'.$annee;
    $json->type = 'Configuration';
    $json->campagne = $annee;

    $json->recolte->no_usages_industriels = 1;
    $json->recolte->no_recapitulatif_couleur = 1;

    $json->recolte->douane->appellation_lieu = '001';
    $json->recolte->douane->type_aoc = '1';
    $json->recolte->douane->couleur = 'B';
    $json->recolte->douane->qualite = 'S ';
    $json->recolte->douane->qualite_vt = 'D7';
    $json->recolte->douane->qualite_sgn = 'D6';

    $json->recolte->certification->genre->appellation_ALSACEBLANC->appellation = "ALSACEBLANC";
	$json->recolte->certification->genre->appellation_ALSACEBLANC->libelle = "AOC Alsace blanc";
    $json->recolte->certification->genre->appellation_ALSACEBLANC->rendement_appellation = 80;
    $json->recolte->certification->genre->appellation_ALSACEBLANC->douane->qualite = '';
        
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

	$json->recolte->certification->genre->appellation_ALSACEBLANC->mention->lieu = $lieu;

    $json->recolte->certification->genre->appellation_KLEVENER->appellation = "KLEVENER";
	$json->recolte->certification->genre->appellation_KLEVENER->libelle = "AOC Klevener de Heiligenstein";
	$json->recolte->certification->genre->appellation_KLEVENER->rendement_appellation = 75;
    $json->recolte->certification->genre->appellation_KLEVENER->no_total_cepage = 1;
	$json->recolte->certification->genre->appellation_KLEVENER->mention->lieu->couleur->cepage_KL->libelle = "Klevener";
	$json->recolte->certification->genre->appellation_KLEVENER->mention->lieu->couleur->cepage_KL->no_vtsgn = 1;
	$json->recolte->certification->genre->appellation_KLEVENER->douane->appellation_lieu = '054';
	$json->recolte->certification->genre->appellation_KLEVENER->douane->code_cepage = '';
    $json->recolte->certification->genre->appellation_KLEVENER->douane->qualite = 'S';

    $json->recolte->certification->genre->appellation_PINOTNOIR->appellation = "PINOTNOIR";
	$json->recolte->certification->genre->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
	$json->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->cepage_PN->libelle = "Pinot noir";
	$json->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->cepage_PN->no_vtsgn = 1;
	$json->recolte->certification->genre->appellation_PINOTNOIR->rendement_appellation = 75;
	$json->recolte->certification->genre->appellation_PINOTNOIR->douane->appellation_lieu = '001';
	$json->recolte->certification->genre->appellation_PINOTNOIR->douane->couleur = 'S';
	$json->recolte->certification->genre->appellation_PINOTNOIR->douane->code_cepage = '1';

    $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->appellation = "PINOTNOIRROUGE";
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace Pinot noir rouge";
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->rendement_appellation = 60;
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->cepage_PR->libelle = "Pinot noir";
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->cepage_PR->no_vtsgn = 1;
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->appellation_lieu = '001';
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->couleur = 'R';
	$json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->code_cepage = '1';

    $grdcru = new stdClass();
    $grdcru->appellation = "GRDCRU";
	$grdcru->libelle = "AOC Alsace Grand Cru";
	$grdcru->rendement = 61;
    $grdcru->douane->qualite = '';

	$grdcru_from_file = new stdClass();

    foreach (file(sfConfig::get('sf_data_dir') . '/import/10/Grdcrv10') as $l) {
        $g = explode(',', preg_replace('/"/', '', $l));
        if ($g[0] == $annee && !isset($g[1]) || $g[1] == "99") {
            continue;
        }
        if (!$g[2]) {
            $grdcru_from_file->{'lieu' . $g[1]}->libelle = $g[3];
            $r = $this->recode_number($g[4]) + $this->recode_number($g[5]);
            //	    echo $g[3]." rendement: $r\n";
            if ($r != $grdcru->rendement)
                $grdcru_from_file->{'lieu' . $g[1]}->rendement = $r;
        }else {
            if (preg_match('/^L/', $g[2]))
                continue;
            $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->libelle = $this->convertCepage2Libelle($g[2]);
            $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->douane->code_cepage = $cepage_douane[3][$g[2]];
            $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->douane->qualite = 'S ';
            $grdcru_from_file->{'lieu' . $g[1]}->douane->appellation_lieu = $g[7];
            if (isset($grdcru_from_file->{'lieu' . $g[1]}->rendement) && $grdcru_from_file->{'lieu' . $g[1]}->rendement != $g[4])
                $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->rendement = $this->recode_number($g[4]) + $this->recode_number($g[5]);
            if ($g[2] == 'ED' || $g[2] == 'SY')
                $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->no_vtsgn = 1;
        }
    }
        
    foreach($grdcru_from_file as $lieu_key => $lieu) {
        $grdcru->mention->{$lieu_key}->libelle = $lieu->libelle;
        if (isset($lieu->rendement)) {
            $grdcru->mention->{$lieu_key}->rendement = $lieu->rendement;
        }
        $grdcru->mention->{$lieu_key}->douane->appellation_lieu = $lieu->douane->appellation_lieu;
        foreach($this->cepage_order as $cepage_key) {
            $cepage = 'cepage_'.$cepage_key;
            if (isset($lieu->couleur->{$cepage})) {
                $grdcru->mention->{$lieu_key}->couleur->{$cepage}->libelle = $lieu->couleur->{$cepage}->libelle;
                $grdcru->mention->{$lieu_key}->couleur->{$cepage}->douane->code_cepage = $lieu->couleur->{$cepage}->douane->code_cepage;
                $grdcru->mention->{$lieu_key}->couleur->{$cepage}->douane->qualite =  $lieu->couleur->{$cepage}->douane->qualite;
                if (isset($grdcru->mention->{$lieu_key}->couleur->{$cepage}->rendement)) {
                    $grdcru->mention->{$lieu_key}->couleur->{$cepage}->rendement = $lieu->couleur->{$cepage}->rendement;
                }
                if (isset($grdcru->mention->{$lieu_key}->couleur->{$cepage}->no_vtsgn)) {
                    $grdcru->mention->{$lieu_key}->couleur->{$cepage}->no_vtsgn = $lieu->couleur->{$cepage}->no_vtsgn;
                }
                unset($lieu->couleur->{$cepage});
            }
        }
        
        foreach($lieu->couleur as $couleur) {
            throw new sfCommandException("Tous les cepages ne sont pas dans le tableau \$this->scepage_order");
        }
    }
        
	$json->recolte->certification->genre->appellation_GRDCRU = $grdcru;

    $json->recolte->certification->genre->appellation_CREMANT->appellation = "CREMANT";
	$json->recolte->certification->genre->appellation_CREMANT->libelle = "AOC Crémant d'Alsace";
	$json->recolte->certification->genre->appellation_CREMANT->douane->qualite = 'M';

    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->douane->qualite = 'M0';

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->libelle = "Pinot Blanc";
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->douane->code_cepage = $cepage_douane[2]['PB'];

    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->libelle = "Chardonnay";
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->douane->code_cepage = $cepage_douane[2]['CD'];

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->libelle = "Pinot Noir Blanc";
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->douane->code_cepage = $cepage_douane[2]['BN'];

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->libelle = "Riesling";
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->douane->code_cepage = $cepage_douane[2]['RI'];

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->libelle = "Pinot Gris";
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->douane->code_cepage = $cepage_douane[2]['PG'];

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->libelle = "Pinot Noir Rosé";
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->couleur = 'S';
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->qualite = 'M ';
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->code_cepage = '1';
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->no_vtsgn = 1;

	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->libelle = "Rebêches";
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->rendement = -1;
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->min_quantite = 0.02;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->max_quantite = 0.1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->no_negociant = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->no_mout = 1;
    $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->no_motif_non_recolte = 1;
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->exclude_total = 1;
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->douane->type_aoc = 4;
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->douane->appellation_lieu = '999';
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->douane->qualite = 'B';
	$json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->douane->code_cepage = '';

	$json->recolte->certification->genre->appellation_CREMANT->rendement_appellation = 80;
    $json->recolte->certification->genre->appellation_CREMANT->mout = 1;
	$json->recolte->certification->genre->appellation_CREMANT->douane->appellation_lieu = '001';

    $json->recolte->certification->genre->appellation_VINTABLE->appellation = "VINTABLE";
    $json->recolte->certification->genre->appellation_VINTABLE->exclude_total = 1;
    $json->recolte->certification->genre->appellation_VINTABLE->no_total_cepage = 1;
	$json->recolte->certification->genre->appellation_VINTABLE->libelle = "Vins sans IG";
	$json->recolte->certification->genre->appellation_VINTABLE->douane->type_aoc = 4;
	$json->recolte->certification->genre->appellation_VINTABLE->douane->appellation_lieu = 999;
	$json->recolte->certification->genre->appellation_VINTABLE->douane->qualite_aoc = '';
    $json->recolte->certification->genre->appellation_VINTABLE->douane->qualite = '';
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->libelle = "Blanc";
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->douane->couleur = "B";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->douane->code_cepage = "";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->no_vtsgn = 1;
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->libelle = "Rosé";
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->douane->couleur = "S";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->douane->code_cepage = "";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->no_vtsgn = 1;
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->libelle = "Rouge";
	$json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->douane->couleur = "R";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->douane->code_cepage = "";
    $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->no_vtsgn = 1;
    $json->recolte->certification->genre->appellation_VINTABLE->rendement = -1;
    $json->recolte->certification->genre->appellation_VINTABLE->rendement_appellation = -1;

	$json->intitule = array("CAVES", "DOMAINE", "EAR", "EARL", "EURL", "GAEC", "GFA, DU", "HERITIERS", "INDIVISION", "M.", "MADAME", "MADEME", "MAISON", "MELLE", "M., ET, MME", "MLLE", "MM.", "MME", "MMES", "MME, VEUVE", "MRS", "S.A.", "SA", "SARL", "S.A.S.", "SAS", "SASU", "S.C.A.", "SCA", "SCEA", "S.C.I.", "SCI", "S.D.F.", "SDF", "SICA", "STE", "STEF", "VEUVE", "VINS");

    $json->motif_non_recolte = array('AE' => "Assemblage Edelzwicker", 'DC' => "Déclaration en cours", 'PC' => "Problème climatique", 'MV' => "Maladie de la vigne", 'MP' => "Motifs personnels", 'VV' => "Vendanges en Vert");

	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = acCouchdbManager::getClient("DR")->find($data->_id, acCouchdbClient::HYDRATE_JSON);
        if ($doc) {
            acCouchdbManager::getClient()->deleteDoc($doc);
        }
	    if (isset($data->delete))
	      continue;
            $doc = acCouchdbManager::getClient()->createDocumentFromData($data);
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
    private function convertCepage2Libelle($c)
    {
        switch ($c) {
            case 'AU':
                return 'Auxerrois';
            case 'RI':
                return 'Riesling';
            case 'GW':
                return 'Gewurzt.';
            case 'PG':
                return 'Pinot Gris';
            case 'MU':
                return "Muscat";
            case 'MO':
                return "Muscat Ottonel";
            case 'ED':
                return 'Assemblage';
            case 'SY':
                return 'Sylvaner';
            default:
                echo "definition for $c missing\n";
                return;
        }
    }

}

