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
            new sfCommandOption('import', null, sfCommandOption::PARAMETER_REQUIRED, 'import type [couchdb|stdout]', 'stdout'),
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
	$docs[] = array('_id'=>'CAMPAGNE_COURANTE', 'CAMPAGNE'=>'2010');

	$json = new stdClass();
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
	$json->recolte->appellation_GRDCRU->libelle = "AOC Grand Cru";
	$lieu = new stdClass();
	$lieu->cepage_RI->libelle = "Riesling";
	$lieu->cepage_GW->libelle = "Gewurztraminer";
	$lieu->cepage_PG->libelle = "Pinot Gris";
	$lieu->cepage_MU->libelle = "Muscat d'Alsace";
	$lieu->cepage_ED->libelle = "Assemblage";
	$json->recolte->appellation_GRDCRU->rendement = 61;
	$json->recolte->appellation_GRDCRU->lieu01 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu01->libelle = "Altenberg de Bergbieten";
	$json->recolte->appellation_GRDCRU->lieu02 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu02->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu02->libelle = "Altenberg de Bergheim";
	$json->recolte->appellation_GRDCRU->lieu03 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu03->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu03->libelle = "Brand";
	$json->recolte->appellation_GRDCRU->lieu04 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu04->libelle = "Eichberg";
	$json->recolte->appellation_GRDCRU->lieu05 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu05->libelle = "Geisberg";
	$json->recolte->appellation_GRDCRU->lieu06 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu06->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu06->libelle = "Gloeckelberg";
	$json->recolte->appellation_GRDCRU->lieu07 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu07->libelle = "Goldert";
	$json->recolte->appellation_GRDCRU->lieu08 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu08->libelle = "Hatschbourg";
	$json->recolte->appellation_GRDCRU->lieu09 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu09->libelle = "Hengst";
	$json->recolte->appellation_GRDCRU->lieu10 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu10->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu10->libelle = "Kanzlerberg";
	$json->recolte->appellation_GRDCRU->lieu11 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu11->libelle = "Kastelberg";
	$json->recolte->appellation_GRDCRU->lieu12 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu12->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu12->libelle = "Kessler";
	$json->recolte->appellation_GRDCRU->lieu13 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu13->libelle = "Kirchberg de Barr";
	$json->recolte->appellation_GRDCRU->lieu14 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu14->libelle = "Kirchberg de Ribeauvillé";
	$json->recolte->appellation_GRDCRU->lieu15 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu15->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu15->libelle = "Kitterlé";
	$json->recolte->appellation_GRDCRU->lieu16 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu16->libelle = "Moenchberg";
	$json->recolte->appellation_GRDCRU->lieu17 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu17->libelle = "Ollwiller";
	$json->recolte->appellation_GRDCRU->lieu18 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu18->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu18->libelle = "Rangen";
	$json->recolte->appellation_GRDCRU->lieu19 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu19->libelle = "Rosacker";
	$json->recolte->appellation_GRDCRU->lieu20 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu20->libelle = "Saering";
	$json->recolte->appellation_GRDCRU->lieu21 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu21->libelle = "Schlossberg";
	$json->recolte->appellation_GRDCRU->lieu22 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu22->libelle = "Sommerberg";
	$json->recolte->appellation_GRDCRU->lieu23 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu23->libelle = "Sonnenglanz";
	$json->recolte->appellation_GRDCRU->lieu24 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu24->libelle = "Spiegel";
	$json->recolte->appellation_GRDCRU->lieu25 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu25->libelle = "Wiebelsberg";
	$json->recolte->appellation_GRDCRU->lieu25->cepage_MU->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu26 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu26->libelle = "Altenberg de Wolxheim";
	$json->recolte->appellation_GRDCRU->lieu27 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu27->libelle = "Engelberg";
	$json->recolte->appellation_GRDCRU->lieu28 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu28->libelle = "Frankstein";
	$json->recolte->appellation_GRDCRU->lieu29 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu29->libelle = "Froehn";
	$json->recolte->appellation_GRDCRU->lieu30 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu30->libelle = "Mambourg";
	$json->recolte->appellation_GRDCRU->lieu31 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu31->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu31->libelle = "Mandelberg";
	$json->recolte->appellation_GRDCRU->lieu32 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu32->libelle = "Marckrain";
	$json->recolte->appellation_GRDCRU->lieu33 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu33->libelle = "Muenchberg";
	$json->recolte->appellation_GRDCRU->lieu34 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu34->libelle = "Osterberg";
	$json->recolte->appellation_GRDCRU->lieu35 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu35->libelle = "Pfersigberg";
	$json->recolte->appellation_GRDCRU->lieu36 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu36->libelle = "Pfingstberg";
	$json->recolte->appellation_GRDCRU->lieu37 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu37->libelle = "Praelatenberg";
	$json->recolte->appellation_GRDCRU->lieu38 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu38->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu38->libelle = "Schoenenbourg";
	$json->recolte->appellation_GRDCRU->lieu39 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu39->libelle = "Sporen";
	$json->recolte->appellation_GRDCRU->lieu40 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu40->libelle = "Steinert";
	$json->recolte->appellation_GRDCRU->lieu41 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu41->libelle = "Steingrubler";
	$json->recolte->appellation_GRDCRU->lieu42 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu42->libelle = "Steinklotz";
	$json->recolte->appellation_GRDCRU->lieu43 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu43->libelle = "Vorbourg";
	$json->recolte->appellation_GRDCRU->lieu44 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu44->libelle = "Wineck-Schlossberg";
	$json->recolte->appellation_GRDCRU->lieu45 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu45->libelle = "Winzenberg";
	$json->recolte->appellation_GRDCRU->lieu46 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu46->libelle = "Zinnkoepflé";
	$json->recolte->appellation_GRDCRU->lieu47 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu47->libelle = "Zotzenberg";
	$json->recolte->appellation_GRDCRU->lieu48 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu48->libelle = "Furstentum";
	$json->recolte->appellation_GRDCRU->lieu49 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu49->rendement = 55;
	$json->recolte->appellation_GRDCRU->lieu49->libelle = "Bruderthal";
	$json->recolte->appellation_GRDCRU->lieu50 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu50->libelle = "Florimont";
	$json->recolte->appellation_GRDCRU->lieu51 = $lieu;
	$json->recolte->appellation_GRDCRU->lieu51->libelle = "Kaefferkopf";
	$json->recolte->appellation_GRDCRU->lieu51->cepage_ED->rendement = 55;

	$json->recolte->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
	$json->recolte->appellation_PINOTNOIR->lieu->cepage_PN->libelle = "Pinot noir";
	$json->recolte->appellation_PINOTNOIR = 75;
	$json->recolte->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace Pinot noir rouge";
	$json->recolte->appellation_PINOTNOIRROUGE->rendement = 60;
	$json->recolte->appellation_PINOTNOIRROUGE->lieu->cepage_PN->libelle = "Pinot noir";

	$json->recolte->appellation_CREMANT->libelle = "AOC Crémant d'Alsace";
	$json->recolte->appellation_CREMANT->lieu->cepage_CD->libelle = "Chardonnay";
	$json->recolte->appellation_CREMANT->lieu->cepage_RS->libelle = "Crémant Rosé";
	$json->recolte->appellation_CREMANT->lieu->cepage_PB->libelle = "Pinot Blanc";
	$json->recolte->appellation_CREMANT->lieu->cepage_PG->libelle = "Pinot Gris";
	$json->recolte->appellation_CREMANT->lieu->cepage_RI->libelle = "Riesling";
	$json->recolte->appellation_CREMANT->rendement = 80;

	$json->recolte->appellation_VINTABLE->libelle = "Vin de table";

	$json->recolte->appellation_KLEVENER->libelle = "Klevener de Heiligenstein";
	$json->recolte->appellation_KLEVENER->rendement = 75;
	$json->recolte->appellation_KLEVENER->lieu->cepage_KL = "Klevener";
	$json->_id = 'CONFIGURATION';

	$docs[] = $json;

	if ($options['import'] == 'couchdb') {
	  foreach ($docs as $data) {
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

}
