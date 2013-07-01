<?php

class importConfiguration2012Task extends sfBaseTask
{

    protected $cepage_order = array("CH", "SY", "AU", "PB", "PI", "ED", "RI", "PG", "MU", "MO", "GW");
    
    protected function configure()
    {
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
        $this->name = 'Configuration2012';
        $this->briefDescription = 'import csv achat file';
        $this->detailedDescription = <<<EOF
The [import|INFO] task does things.
Call it with:

  [php symfony import|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        ini_set('memory_limit', '512M');
        set_time_limit('3600');
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        if ($options['removedb'] == 'yes' && $options['import'] == 'couchdb') {
            if (acCouchdbManager::getClient()->databaseExists()) {
                acCouchdbManager::getClient()->deleteDatabase();
            }
            acCouchdbManager::getClient()->createDatabase();
        }

        foreach (file(sfConfig::get('sf_data_dir') . '/import/12/Ceprec12') as $a) {
            $csv = explode(',', preg_replace('/"/', '', $a));
            $cepage_douane[$csv[1]][$csv[0]] = $csv[14];
        }

        $rendement_couleur_blanc_lieux_dits = 68;
        $rendement_couleur_rouge_lieux_dits = 60;

        $rendement_couleur_blanc_communale = 72;
        $rendement_couleur_rouge_communale = 60;

        $rendement_couleur_blanc_cremant = 80;
        $rendement_couleur_rose_cremant = 80;

        $annee = "2012";

        $json = new stdClass();
        $json->_id = 'CONFIGURATION-' . $annee;
        $json->type = 'Configuration';
        $json->campagne = $annee;

        $json->recolte->douane->appellation_lieu = '001';
        $json->recolte->douane->type_aoc = '1';
        $json->recolte->douane->couleur = 'B';
        $json->recolte->douane->qualite = 'S ';
        $json->recolte->douane->qualite_vt = 'D7';
        $json->recolte->douane->qualite_sgn = 'D6';

        $json->recolte->certification->genre->appellation_ALSACEBLANC->appellation = "ALSACEBLANC";
        $json->recolte->certification->genre->appellation_ALSACEBLANC->libelle = "AOC Alsace blanc";
        $json->recolte->certification->genre->appellation_ALSACEBLANC->rendement_appellation = 80;
        $json->recolte->certification->genre->appellation_ALSACEBLANC->douane->qualite = 'S ';

        $lieu = new stdClass();
        $lieu->couleur->cepage_CH = $this->getCepage('CH', $cepage_douane[1]['CH'], null, true);
        $lieu->couleur->cepage_SY = $this->getCepage('SY', $cepage_douane[1]['SY'], null, true);
        $lieu->couleur->cepage_AU = $this->getCepage('AU', $cepage_douane[1]['AU'], 'S0', true);
        $lieu->couleur->cepage_PB = $this->getCepage('PB', $cepage_douane[1]['PB'], 'S0', true);
        $lieu->couleur->cepage_PI = $this->getCepage('PI', $cepage_douane[1]['PI'], null, true);
        $lieu->couleur->cepage_ED = $this->getCepage('ED', $cepage_douane[1]['ED'], null, true);
        $lieu->couleur->cepage_RI = $this->getCepage('RI', $cepage_douane[1]['RI'], null, true);
        $lieu->couleur->cepage_PG = $this->getCepage('PG', $cepage_douane[1]['PG'], null, true);
        $lieu->couleur->cepage_MU = $this->getCepage('MU', $cepage_douane[1]['MU'], null, true);
        $lieu->couleur->cepage_MO = $this->getCepage('MO', $cepage_douane[1]['MO'], 'S0', true);
        $lieu->couleur->cepage_GW = $this->getCepage('GW', $cepage_douane[1]['GW'], null, true);

        $json->recolte->certification->genre->appellation_ALSACEBLANC->mention->lieu = $lieu;

        $json->recolte->certification->genre->appellation_LIEUDIT->appellation = "LIEUDIT";
        $json->recolte->certification->genre->appellation_LIEUDIT->libelle = "AOC Alsace Lieu-dit";
        $json->recolte->certification->genre->appellation_LIEUDIT->detail_lieu_editable = 1;
        $json->recolte->certification->genre->appellation_LIEUDIT->douane->appellation_lieu = '070';
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->douane->qualite = 'S ';
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->douane->couleur = "B";
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_lieux_dits;
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->libelle = "Blanc";
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_CH = $this->getCepage('CH', $cepage_douane[8]['CH'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_SY = $this->getCepage('SY', $cepage_douane[8]['SY'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_AU = $this->getCepage('AU', $cepage_douane[8]['AU'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_PB = $this->getCepage('PB', $cepage_douane[8]['PB'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_PI = $this->getCepage('PI', $cepage_douane[8]['PI'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_ED = $this->getCepage('ED', $cepage_douane[8]['ED'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_RI = $this->getCepage('RI', $cepage_douane[8]['RI'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_PG = $this->getCepage('PG', $cepage_douane[8]['PG'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_MU = $this->getCepage('MU', $cepage_douane[8]['MU'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_MO = $this->getCepage('MO', $cepage_douane[8]['MO'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurBlanc->cepage_GW = $this->getCepage('GW', $cepage_douane[8]['GW'], 'S0');
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurRouge->douane->couleur = "R";
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurRouge->rendement_couleur = $rendement_couleur_rouge_lieux_dits;
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurRouge->libelle = "Rouge";
        $json->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleurRouge->cepage_PR = $this->getCepage('PR', $cepage_douane[8]['PR']);

        $cepcom = array();
        foreach (file(sfConfig::get('sf_data_dir') . '/import/12/Cepcom12') as $l) {
            $csv = explode(',', preg_replace('/"/', '', $l));
            if ($csv[0] != $annee) {
                continue;
            }
            
            $cepcom[$csv[1]][$csv[2]] = $csv[7];  
        }

        $appellation = new stdClass();

        $appellation->appellation = "COMMUNALE";
        $appellation->libelle = "AOC Alsace Communale";

        $appellation->mention->lieuBLIE->libelle = "Blienschwiller";
        $appellation->mention->lieuBLIE->couleurBlanc->douane->appellation_lieu = $cepcom['BLIE']['SY'];
        $appellation->mention->lieuBLIE->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuBLIE->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuBLIE->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuBLIE->couleurBlanc->cepage_SY = $this->getCepage('SY', $cepage_douane[7]['SY'], 'S ');

        $appellation->mention->lieuBARR->libelle = "Côtes de Barr";
        $appellation->mention->lieuBARR->couleurBlanc->douane->appellation_lieu = $cepcom['BARR']['SY'];
        $appellation->mention->lieuBARR->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuBARR->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuBARR->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuBARR->couleurBlanc->cepage_SY = $this->getCepage('SY', $cepage_douane[7]['SY'], 'S ');

        $appellation->mention->lieuROUF->libelle = "Côte de Rouffach";
        $appellation->mention->lieuROUF->couleurBlanc->douane->appellation_lieu = $cepcom['ROUF']['AL'];
        $appellation->mention->lieuROUF->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuROUF->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuROUF->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuROUF->couleurBlanc->cepage_RI = $this->getCepage('RI', $cepage_douane[7]['RI'], 'S0');
        $appellation->mention->lieuROUF->couleurBlanc->cepage_PG = $this->getCepage('PG', $cepage_douane[7]['PG'], 'S0');
        $appellation->mention->lieuROUF->couleurBlanc->cepage_GW = $this->getCepage('GW', $cepage_douane[7]['GW'], 'S0');
        $appellation->mention->lieuROUF->couleurRouge->douane->appellation_lieu = $cepcom['ROUF']['PR'];
        $appellation->mention->lieuROUF->couleurRouge->douane->couleur = 'R';
        $appellation->mention->lieuROUF->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->mention->lieuROUF->couleurRouge->libelle = "Rouge";
        $appellation->mention->lieuROUF->couleurRouge->cepage_PR = $this->getCepage('PR', $cepage_douane[7]['PR'], 'S ');

        $appellation->mention->lieuKLEV->libelle = "Klevener de Heiligenstein";
        $appellation->mention->lieuKLEV->couleurBlanc->douane->appellation_lieu = $cepcom['KLEV']['KL'];
        $appellation->mention->lieuKLEV->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuKLEV->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuKLEV->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuKLEV->couleurBlanc->cepage_KL->libelle = "Klevener";
        $appellation->mention->lieuKLEV->couleurBlanc->cepage_KL->libelle_long = "Klevener de Heiligenstein";
        $appellation->mention->lieuKLEV->couleurBlanc->cepage_KL->douane->code_cepage = $cepage_douane[7]['KL'];
        $appellation->mention->lieuKLEV->couleurBlanc->cepage_KL->douane->qualite = 'S ';
        $appellation->mention->lieuKLEV->couleurBlanc->cepage_KL->no_vtsgn = 1;

        $appellation->mention->lieuOTTR->libelle = "Ottrott";
        $appellation->mention->lieuOTTR->couleurRouge->douane->appellation_lieu = $cepcom['OTTR']['PR'];
        $appellation->mention->lieuOTTR->couleurRouge->douane->couleur = 'R';
        $appellation->mention->lieuOTTR->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->mention->lieuOTTR->couleurRouge->libelle = "Rouge";
        $appellation->mention->lieuOTTR->couleurRouge->cepage_PR = $this->getCepage('PR', $cepage_douane[7]['PR'], 'S ');

        $appellation->mention->lieuRODE->libelle = "Rodern";
        $appellation->mention->lieuRODE->couleurRouge->douane->appellation_lieu = $cepcom['RODE']['PR'];
        $appellation->mention->lieuRODE->couleurRouge->douane->couleur = 'R';
        $appellation->mention->lieuRODE->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->mention->lieuRODE->couleurRouge->libelle = "Rouge";
        $appellation->mention->lieuRODE->couleurRouge->cepage_PR = $this->getCepage('PR', $cepage_douane[7]['PR'], 'S ');

        $appellation->mention->lieuSTHI->libelle = "Saint Hippolyte";
        $appellation->mention->lieuSTHI->couleurRouge->douane->appellation_lieu = $cepcom['STHI']['PR'];
        $appellation->mention->lieuSTHI->couleurRouge->douane->couleur = 'R';
        $appellation->mention->lieuSTHI->couleurRouge->rendement_couleur = $rendement_couleur_rouge_communale;
        $appellation->mention->lieuSTHI->couleurRouge->libelle = "Rouge";
        $appellation->mention->lieuSTHI->couleurRouge->cepage_PR = $this->getCepage('PR', $cepage_douane[7]['PR'], 'S ');

        $appellation->mention->lieuNOBL->libelle = "Vallée Noble";
        $appellation->mention->lieuNOBL->couleurBlanc->douane->appellation_lieu = $cepcom['NOBL']['AL'];
        $appellation->mention->lieuNOBL->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuNOBL->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuNOBL->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuNOBL->couleurBlanc->cepage_RI = $this->getCepage('RI', $cepage_douane[7]['RI'], 'S0');
        $appellation->mention->lieuNOBL->couleurBlanc->cepage_PG = $this->getCepage('PG', $cepage_douane[7]['PG'], 'S0');
        $appellation->mention->lieuNOBL->couleurBlanc->cepage_GW = $this->getCepage('GW', $cepage_douane[7]['GW'], 'S0');

        $appellation->mention->lieuSTGR->libelle = "Val Saint Grégoire";
        $appellation->mention->lieuSTGR->couleurBlanc->douane->appellation_lieu = $cepcom['STGR']['AL'];
        $appellation->mention->lieuSTGR->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuSTGR->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuSTGR->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuSTGR->couleurBlanc->cepage_AU = $this->getCepage('AU', $cepage_douane[7]['AU'], 'S0');
        $appellation->mention->lieuSTGR->couleurBlanc->cepage_PB = $this->getCepage('PB', $cepage_douane[7]['PB'], 'S0');
        $appellation->mention->lieuSTGR->couleurBlanc->cepage_PG = $this->getCepage('PG', $cepage_douane[7]['PG'], 'S0');

        $appellation->mention->lieuSCHE->libelle = "Scherwiller";
        $appellation->mention->lieuSCHE->couleurBlanc->douane->appellation_lieu = $cepcom['SCHE']['RI'];
        $appellation->mention->lieuSCHE->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuSCHE->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuSCHE->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuSCHE->couleurBlanc->cepage_RI = $this->getCepage('RI', $cepage_douane[7]['RI'], 'S ');

        $appellation->mention->lieuWOLX->libelle = "Wolxheim";
        $appellation->mention->lieuWOLX->couleurBlanc->douane->appellation_lieu = $cepcom['WOLX']['RI'];
        $appellation->mention->lieuWOLX->couleurBlanc->douane->couleur = 'B';
        $appellation->mention->lieuWOLX->couleurBlanc->rendement_couleur = $rendement_couleur_blanc_communale;
        $appellation->mention->lieuWOLX->couleurBlanc->libelle = "Blanc";
        $appellation->mention->lieuWOLX->couleurBlanc->cepage_RI = $this->getCepage('RI', $cepage_douane[7]['RI'], 'S ');

        $json->recolte->certification->genre->appellation_COMMUNALE = $appellation;

        $grdcru = new stdClass();
        $grdcru->appellation = "GRDCRU";
        $grdcru->libelle = "AOC Alsace Grands Crus";
        $grdcru->rendement = 61;
        $grdcru->douane->qualite = '';
        
        $grdcru_from_file = new stdClass();

        foreach (file(sfConfig::get('sf_data_dir') . '/import/12/Grdcrv12') as $l) {
            $g = explode(',', preg_replace('/"/', '', $l));
            if ($g[0] == $annee && !isset($g[1]) || $g[1] == "99") {
                continue;
            }
            if (!$g[2]) {
                $grdcru_from_file->{'lieu' . $g[1]}->libelle = $g[3];
                $r = $this->recode_number($g[4]) + $this->recode_number($g[5]);
                //      echo $g[3]." rendement: $r\n";
                if ($r != $grdcru->rendement)
                    $grdcru_from_file->{'lieu' . $g[1]}->rendement = $r;
            }else {
                if (preg_match('/^L/', $g[2]))
                    continue;
                $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->libelle = $this->convertCepage2Libelle($g[2]);
                if($this->convertCepage2LibelleLong($g[2])) {
                    $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->libelle_long = $this->convertCepage2LibelleLong($g[2]);
                }
                $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->douane->code_cepage = $cepage_douane[3][$g[2]];
                $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->douane->qualite = 'S ';
                if ($g[2] == 'MO') {
                    $grdcru_from_file->{'lieu' . $g[1]}->couleur->{'cepage_' . $g[2]}->douane->qualite = 'S0';    
                }
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
                    if(isset($lieu->couleur->{$cepage}->libelle_long)) {
                        $grdcru->mention->{$lieu_key}->couleur->{$cepage}->libelle_long = $lieu->couleur->{$cepage}->libelle_long;
                    }
                    $grdcru->mention->{$lieu_key}->couleur->{$cepage}->douane->code_cepage = $lieu->couleur->{$cepage}->douane->code_cepage;
                    $grdcru->mention->{$lieu_key}->couleur->{$cepage}->douane->qualite =  $lieu->couleur->{$cepage}->douane->qualite;
                    if (isset($lieu->couleur->{$cepage}->rendement)) {
                        $grdcru->mention->{$lieu_key}->couleur->{$cepage}->rendement = $lieu->couleur->{$cepage}->rendement;
                    }
                    if (isset($lieu->couleur->{$cepage}->no_vtsgn)) {
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

        $json->recolte->certification->genre->appellation_PINOTNOIR->appellation = "PINOTNOIR";
        $json->recolte->certification->genre->appellation_PINOTNOIR->libelle = "AOC Alsace Pinot noir";
        $json->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->cepage_PN = $this->getCepage('PN');
        $json->recolte->certification->genre->appellation_PINOTNOIR->no_vtsgn = 1;
        $json->recolte->certification->genre->appellation_PINOTNOIR->rendement_appellation = 75;
        $json->recolte->certification->genre->appellation_PINOTNOIR->douane->appellation_lieu = '001';
        $json->recolte->certification->genre->appellation_PINOTNOIR->douane->couleur = 'S';
        $json->recolte->certification->genre->appellation_PINOTNOIR->douane->code_cepage = '1';

        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->appellation = "PINOTNOIRROUGE";
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->libelle = "AOC Alsace PN rouge";
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->rendement_appellation = 60;
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->cepage_PR = $this->getCepage('PR');
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->no_vtsgn = 1;
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->appellation_lieu = '001';
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->couleur = 'R';
        $json->recolte->certification->genre->appellation_PINOTNOIRROUGE->douane->code_cepage = '1';

        $json->recolte->certification->genre->appellation_CREMANT->appellation = "CREMANT";
        $json->recolte->certification->genre->appellation_CREMANT->auto_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->libelle = "AOC Crémant d'Alsace";
        $json->recolte->certification->genre->appellation_CREMANT->rendement_appellation = 80;
        $json->recolte->certification->genre->appellation_CREMANT->douane->qualite = 'MST';
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->douane->qualite = 'M0';
        $json->recolte->certification->genre->appellation_CREMANT->mout = 1;
        $json->recolte->certification->genre->appellation_CREMANT->no_vtsgn = 1;
        $json->recolte->certification->genre->appellation_CREMANT->douane->appellation_lieu = '001';

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->libelle = "" ;

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->libelle = "Pinot Blanc";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PB->douane->code_cepage = $cepage_douane[2]['PB'];

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->libelle = "Chardonnay";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_CD->douane->code_cepage = $cepage_douane[2]['CD'];

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->libelle = "Pinot Noir Blanc";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BN->douane->code_cepage = $cepage_douane[2]['BN'];

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->libelle = "Riesling";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RI->douane->code_cepage = $cepage_douane[2]['RI'];

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->libelle = "Pinot Gris";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PG->douane->code_cepage = $cepage_douane[2]['PG'];

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->libelle = "Pinot Noir Rosé";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->no_ds = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->couleur = 'S';
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->qualite = 'M ';
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_PN->douane->code_cepage = '';

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->libelle = "Rebêches";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RB->no_ds = 1;
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

        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BL->libelle = "Blanc";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_BL->no_dr = 1;
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RS->libelle = "Rosé";
        $json->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->cepage_RS->no_dr = 1;

        $json->recolte->certification->genre->appellation_VINTABLE->appellation = "VINTABLE";
        $json->recolte->certification->genre->appellation_VINTABLE->auto_ds = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->exclude_total = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->no_total_cepage = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->libelle = "Vins sans IG";
        $json->recolte->certification->genre->appellation_VINTABLE->no_vtsgn = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->douane->type_aoc = 4;
        $json->recolte->certification->genre->appellation_VINTABLE->douane->appellation_lieu = '999';
        $json->recolte->certification->genre->appellation_VINTABLE->douane->qualite_aoc = '';
        $json->recolte->certification->genre->appellation_VINTABLE->douane->qualite = '';
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->libelle = "Blanc";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->no_ds = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->douane->couleur = "B";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_BL->douane->code_cepage = "";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->libelle = "Rosé";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->no_ds = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->douane->couleur = "S";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RS->douane->code_cepage = "";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->libelle = "Rouge";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->no_ds = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->douane->couleur = "R";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_RG->douane->code_cepage = "";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_VINTABLE->libelle = "Sans IG";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_VINTABLE->no_dr = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_VINTABLE->douane->couleur = "VDT";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_VINTABLE->douane->code_cepage = "";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_MS->libelle = "Mousseux";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_MS->no_dr = 1;
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_MS->douane->couleur = "M";
        $json->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_MS->douane->code_cepage = "";

        $json->recolte->certification->genre->appellation_VINTABLE->rendement = -1;
        $json->recolte->certification->genre->appellation_VINTABLE->rendement_appellation = -1;

        $json->intitule = array("CAVES", "DOMAINE", "EAR", "EARL", "EURL", "GAEC", "GFA, DU", "HERITIERS", "INDIVISION", "M.", "MADAME", "MADEME", "MAISON", "MELLE", "M., ET, MME", "MLLE", "MM.", "MME", "MMES", "MME, VEUVE", "MRS", "S.A.", "SA", "SARL", "S.A.S.", "SAS", "SASU", "S.C.A.", "SCA", "SCEA", "S.C.I.", "SCI", "S.D.F.", "SDF", "SICA", "STE", "STEF", "VEUVE", "VINS");

        $json->motif_non_recolte = array('AE' => "Assemblage Edelzwicker", 'DC' => "Déclaration en cours", 'PC' => "Problème climatique", 'MV' => "Maladie de la vigne", 'MP' => "Motifs personnels", 'VV' => "Vendanges en Vert");

        $docs[] = $json;

        $json = new stdClass();
        $json->_id = 'CURRENT';
        $json->type = 'Current';
        $json->campagne = $annee;
        $json->dr_non_editable = 1;
        $json->dr_non_ouverte = 1;
        $json->ds_non_editable = 1;
        $json->ds_non_ouverte = 0;
        $json->declaration_courante = 'DS';
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

    private function recode_number($val)
    {
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

    private function convertCepage2LibelleLong($c)
    {
        switch ($c) {
            case 'GW':

                return 'Gewurztraminer';
            default:
                
                return null;
        }
    }

    public function getCepage($code, $code_depage = null, $qualite = null, $rendement = null)
    {

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

        $cepages->cepage_MU->libelle = "Muscat d'Alsace";
        $cepages->cepage_MU->rendement = 90;
        $cepages->cepage_MU->douane->code_cepage = $code_depage;

        $cepages->cepage_MO->libelle = "Muscat Ottonel";
        $cepages->cepage_MO->rendement = 90;
        $cepages->cepage_MO->douane->code_cepage = $code_depage;

        $cepages->cepage_GW->libelle = "Gewurzt.";
        $cepages->cepage_GW->libelle_long = "Gewurztraminer";
        $cepages->cepage_GW->rendement = 80;
        $cepages->cepage_GW->douane->code_cepage = $code_depage;

        $cepages->cepage_PN->libelle = "Pinot Noir";
        $cepages->cepage_PN->libelle_long = "Pinot Noir Rosé";
        $cepages->cepage_PN->no_vtsgn = 1;
        $cepages->cepage_PN->douane->code_cepage = $code_depage;

        $cepages->cepage_PR->libelle = "Pinot Noir";
        $cepages->cepage_PR->libelle_long = "Pinot Noir Rouge";
        $cepages->cepage_PR->no_vtsgn = 1;
        $cepages->cepage_PR->douane->code_cepage = $code_depage;

        $code_entier = "cepage_" . $code;

        if (isset($cepages->{$code_entier})) {
            $cepage = $cepages->{$code_entier};
            if (!$rendement && isset($cepage->rendement)) {
                $cepage->rendement = null;
            }
            if (!$code_depage && isset($code_depage->douane->code_cepage)) {
                $code_depage->douane->code_cepage = null;
            }
            if(!is_null($qualite)) {
                $cepage->douane->qualite = $qualite;
            }
            return $cepage;
        } else {
            throw new sfCommandException("Cépage does not exist : " . $code);
        }
    }

}