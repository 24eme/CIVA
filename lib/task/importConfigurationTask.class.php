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

	$docs = array();
	$json = new stdClass();
	$json->_id = 'CAMPAGNE_COURANTE';
	$json->campagne = "2010";
	//	$docs[] = $json;

	$json = new stdClass();
        $json->recolte->appellation_ALSACEBLANC->appellation = "ALSACEBLANC";
	$json->recolte->appellation_ALSACEBLANC->libelle = "AOC Alsace blanc";
	$lieu = new stdClass();

	$lieu->cepage_PG->libelle = "Pinot Gris";
	$lieu->cepage_GW->libelle = "Gewurztraminer";
	$lieu->cepage_PG->rendement = 80;
	$lieu->cepage_GW->rendement = 80;

	$lieu->cepage_MU->libelle = "Muscat d'Alsace";
	$lieu->cepage_RI->libelle = "Riesling";
	$lieu->cepage_MU->rendement = 90;
	$lieu->cepage_RI->rendement = 90;

	$lieu->cepage_SY->libelle = "Sylvaner";
	$lieu->cepage_PB->libelle = "Pinot Blanc";
	$lieu->cepage_CH->libelle = "Chasselas";
	$lieu->cepage_SY->rendement = 100;
	$lieu->cepage_PB->rendement = 100;
	$lieu->cepage_CH->rendement = 100;

	$lieu->cepage_ED->libelle = "Edelzwicker";

	$json->recolte->appellation_ALSACEBLANC->lieu = $lieu;

	$grdcru = new stdClass();
        $grdcru->appellation = "GRDCRU";
	$grdcru->libelle = "AOC Alsace Grand Cru";

	foreach(file(sfConfig::get('sf_data_dir') . '/' . 'Grdcrv09') as $l) {
	  $g = explode(',', preg_replace('/"/', '', $l));
	  if ($g[1] == "99")  {
	    continue;
	  }
	  if (!$g[2]) {
	    $grdcru->{'lieu'.$g[1]}->libelle = $g[3];
	    $grdcru->{'lieu'.$g[1]}->rendement = $this->recode_number($g[4]);
	  }else{
	    if (preg_match('/^L/', $g[2]))
	      continue;
	    $grdcru->{'lieu'.$g[1]}->{'cepage_'.$g[2]}->libelle = $this->convertCepage2Libelle($g[2]);	
	    if ($grdcru->{'lieu'.$g[1]}->rendement != $g[4])
	      $grdcru->{'lieu'.$g[1]}->{'cepage_'.$g[2]}->rendement = $this->recode_number($g[4]);
  	  }
	  
	}
	$json->recolte->appellation_GRDCRU = $grdcru;

        $json->recolte->appellation_PINOTNOIR->appellation = "PINOTNOIR";
	$json->recolte->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
	$json->recolte->appellation_PINOTNOIR->lieu->cepage_PN->libelle = "Pinot noir";
	$json->recolte->appellation_PINOTNOIR->rendement = 75;

        $json->recolte->appellation_PINOTNOIRROUGE->appellation = "PINOTNOIRROUGE";
	$json->recolte->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace Pinot noir rouge";
	$json->recolte->appellation_PINOTNOIRROUGE->rendement = 60;
	$json->recolte->appellation_PINOTNOIRROUGE->lieu->cepage_PN->libelle = "Pinot noir";

        $json->recolte->appellation_CREMANT->appellation = "CREMANT";
	$json->recolte->appellation_CREMANT->libelle = "AOC Crémant d'Alsace";
	$json->recolte->appellation_CREMANT->lieu->cepage_PN->libelle = "Pinot Noir";
	$json->recolte->appellation_CREMANT->lieu->cepage_CD->libelle = "Chardonnay";
	$json->recolte->appellation_CREMANT->lieu->cepage_RS->libelle = "Crémant Rosé";
	$json->recolte->appellation_CREMANT->lieu->cepage_PB->libelle = "Pinot Blanc";
	$json->recolte->appellation_CREMANT->lieu->cepage_PG->libelle = "Pinot Gris";
	$json->recolte->appellation_CREMANT->lieu->cepage_RI->libelle = "Riesling";
	$json->recolte->appellation_CREMANT->rendement = 80;
        $json->recolte->appellation_CREMANT->mout = 1;

        $json->recolte->appellation_VINTABLE->appellation = "VINTABLE";
	$json->recolte->appellation_VINTABLE->libelle = "Vin de table";

        $json->recolte->appellation_KLEVENER->appellation = "KLEVENER";
	$json->recolte->appellation_KLEVENER->libelle = "Klevener de Heiligenstein";
	$json->recolte->appellation_KLEVENER->rendement = 75;
	$json->recolte->appellation_KLEVENER->lieu->cepage_KL->libelle = "Klevener";
	$json->_id = 'CONFIGURATION';
	$json->type = 'Configuration';

	$json->intitule = array("CAVES", "DOMAINE", "EAR", "EARL", "EURL", "GAEC", "GFA, DU", "HERITIERS", "INDIVISION", "M.", "MADAME", "MADEME", "MAISON", "MELLE", "M., ET, MME", "MLLE", "MM.", "MME", "MMES", "MME, VEUVE", "MRS", "S.A.", "SA", "SARL", "S.A.S.", "SAS", "SASU", "S.C.A.", "SCA", "SCEA", "S.C.I.", "SCI", "S.D.F.", "SDF", "SICA", "STE", "STEF", "VEUVE", "VINS");

	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
	    $doc = sfCouchdbManager::getClient()->retrieveDocumentById($data->_id);
	    if ($doc) {
	      $doc->load($data);
	    }else{
	      $doc = sfCouchdbManager::getClient()->createDocumentFromData($data);
	    }
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
	return 'Gewurztraminer';
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
