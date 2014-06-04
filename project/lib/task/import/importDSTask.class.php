<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class importDSTask
 * @author mathurin
 */
class importDSTask extends importAbstractTask
{

    protected $conf = null;
    protected $current = null;
    protected $ds_negoce = null;
    
    protected $error_term = "\033[31mERREUR:\033[0m";
    protected $warning_term = "\033[33m----->ATTENTION:\033[0m ";

    const CSV_DS_ID = 0;
    const CSV_DS_ANNEE = 1;
    const CSV_DS_TYPE_DECLARATION = 2; // 'P' ou 'N'
    const CSV_DS_LIEUDESTOCKAGE = 3; // 30 chars
    const CSV_DS_LIEU_PRINCIPAL = 4; // 'P' ou 'S'
    const CSV_DS_TNUM_CIVAGEN = 5; // ?
    const CSV_DS_CVI = 6;
    const CSV_DS_TCIVAB_CIVAGEN = 7; // vaut '0' la plupart du temps
    const CSV_DS_VOLUME_STOCK_ALSACE = 8;
    const CSV_DS_VOLUME_VT_ALSACE = 9;
    const CSV_DS_VOLUME_SGN_ALSACE = 10;
    const CSV_DS_VOLUME_STOCK_GRDCRU = 11;
    const CSV_DS_VOLUME_VT_GRDCRU = 12;
    const CSV_DS_VOLUME_SGN_GRDCRU = 13;
    const CSV_DS_VOLUME_CREMANT = 14;
    const CSV_DS_VOLUME_TOTAL_STOCK = 15;
    const CSV_DS_VOLUME_TOTAL_VT = 16;
    const CSV_DS_VOLUME_TOTAL_SGN = 17;
    const CSV_DS_VOLUME_VINTABLE = 18;
    const CSV_DS_VOLUME_MOUTS = 19;
    const CSV_DS_VOLUME_DPLC = 20;
    const CSV_DS_VOLUME_REBECHES = 21;
    const CSV_DS_TRAITEE = 22; // "O" ou "N"
    const CSV_DS_DATE_SAISIE = 23; // JJMMAAAA
    // PRODUITS
    const CSV_PRODUIT_APPELLATION = 24; // "1", "2", "3"
    const CSV_PRODUIT_CEPAGE = 25; // "ED", "GW"
    const CSV_PRODUIT_COULEUR = 26; // "BL", "RS"
    const CSV_PRODUIT_LIEUDIT = 27; // "23"... 
    const CSV_PRODUIT_ORDRE_AFFICHAGE = 28;
    const CSV_PRODUIT_VOLUME_STOCK = 29;
    const CSV_PRODUIT_VOLUME_VT = 30;
    const CSV_PRODUIT_VOLUME_SGN = 31;
    const CSV_PRODUIT_VOLUME_TOTAL_CEPAGE = 32;

    protected function configure()
    {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('ds-negoce', null, sfCommandOption::PARAMETER_OPTIONAL, 'Ds negoce', '0'),
        ));

        $this->namespace = 'import';
        $this->name = 'DS';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [importDS|INFO] task does things.
Call it with:

  [php symfony importDS|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);
        $this->ds_negoce = array_key_exists('ds-negoce', $options) && $options['ds-negoce'];
        set_time_limit(0);
        $file = file($arguments['file']);


        echo "\n*** Transformation des CSV en tableau de DS ***";
        $dss = $this->transformFileInDSS($file);
        $dss = $this->sortDSSArrayRows($dss);

        if ($this->ds_negoce) {
            echo "\n*** CREATION DES LIEUX DE STOCKAGE ***";
            $this->createLieuxStockageForDS($dss);
        }

        echo "\n*** Import des DS ***\n";
        $this->importDSS($dss);
        echo "\n*** FIN DE l'Import des DS ***\n";
    }

    protected function transformFileInDSS($file)
    {
        $dss = array();
        foreach ($file as $line) {
            $datas = str_getcsv($line, ',');
            $id_ds = $datas[self::CSV_DS_ID];
            if (!array_key_exists($id_ds, $dss))
                $dss[$id_ds] = array();
            if (count($datas) > 25) {
                // $ordre_affichage = $datas[self::CSV_PRODUIT_ORDRE_AFFICHAGE];
                $dss[$id_ds][] = $line;
            } else {
                $dss[$id_ds][] = $line;
            }
        }
        return $dss;
    }

    protected function sortDSSArrayRows($dss)
    {
        foreach ($dss as $key => $ds) {
            ksort($ds, SORT_NUMERIC);
            $dss[$key] = array_values($ds);
        }
        return $dss;
    }

    protected function importDSS($dss)
    {

        $ds_client = DSCivaClient::getInstance();
        $tiersListIDs = array();
        $type_ds = ($this->ds_negoce) ? DSCivaClient::TYPE_DS_NEGOCE : DSCivaClient::TYPE_DS_PROPRIETE;

        foreach ($dss as $id_ds => $ds_csv) {
            $ds = new DSCiva();
            $ds->add('type_ds', $type_ds);
            if (!count($ds_csv))
                throw new sfException("La ligne d'identifiant $id_ds n'est pas valide.");
            $ds_ligne = $ds_csv[0];
            $ds_csv_datas = str_getcsv($ds_ligne, ',');

            $date = date_format($this->convertToDateObject($ds_csv_datas[self::CSV_DS_DATE_SAISIE]), 'Y-m-d');
            $periode = $this->getCurrent()->getPeriodeDS();//$ds_client->buildPeriode($date);
            $ds->date_emission = $date;
            $ds->date_stock = $date;
            $ds->numero_archive = substr($ds_csv_datas[self::CSV_DS_ID], 2);
            $ds->identifiant = ($this->ds_negoce) ? 'C'.$ds_csv_datas[self::CSV_DS_TCIVAB_CIVAGEN] : $ds_csv_datas[self::CSV_DS_CVI];
            $tiers = $ds->getEtablissement();
            $identifiant = $tiers->getIdentifiant();
            if (!in_array($tiers->_id, $tiersListIDs)) {
                $removeDss = DSCivaClient::getInstance()->removeAllDssByCvi($tiers, $date);
                if ($removeDss) {
                    echo " Les DS suivantes ont été suprimées : ";
                    foreach ($removeDss as $removeDsID) {
                        echo $this->green($removeDsID) . " ";
                    }
                    echo " \n";
                }
                $tiersListIDs[] = $identifiant;
            }

            if (is_null($tiers)) {
                echo $this->error_term . " Le tiers de CVI $identifiant n'a pas été trouvé \n";
                continue;
            } else {
                $num_lieu = null;
                if ($ds_csv_datas[self::CSV_DS_LIEU_PRINCIPAL] == "P") {
                    if (!$tiers->exist('lieux_stockage')) {
                        echo $this->error_term . " Absence de lieu de stockage pour $identifiant \n";
                        continue;
                    }
                    $lieu_principal = $tiers->getLieuStockagePrincipal();
                    if (!$lieu_principal) {
                        echo $this->error_term . " Absence de lieu Principal pour $identifiant \n";
                        continue;
                    }
                    $num_lieu = $lieu_principal->getNumeroIncremental();
                } else {
                    $num_lieu = $ds_client->getNextLieuStockageSecondaireByIdentifiantAndDate($ds->identifiant, $date);
                }
                if (!$num_lieu) {
                    echo $this->error_term . " Le lieu de stockage n'a pas pu être déterminé pour $identifiant \n";
                    continue;
                }
                $ds->_id = sprintf('DS-%s-%s-%s', $ds->identifiant, $periode, $num_lieu);
            }
            try {
                $ds->storeInfos();
            } catch (sfException $e) {
                echo $this->error_term . " pour la DS $id_ds : " . $e->getMessage() . "\n";
                continue;
            }

            // Gestion des VinSansIg
            $this->importVinSansIg($ds_csv_datas, $ds);

            //    Autres
            if ($ds->isDsPrincipale()) {
                $rebeche = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_REBECHES]);
                $dplc = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_DPLC]);
                $mouts = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_MOUTS]);
                $ds->updateAutre($rebeche, $dplc, 0, $mouts);
            }

            //  Etape
            if ($this->convertOuiNon($ds_csv_datas[self::CSV_DS_TRAITEE])) {
                $ds->add('num_etape', 6);
                $ds->validate($ds->date_emission);
            } else
                $ds->add('num_etape', 5);
            // Produits
            if (count($ds_csv) == 1 && count($ds_csv_datas) < 25) {
                try {
                    $ds->save();
                    echo "La DS " . $this->green($ds->_id) . " a été sauvée et est une " . $this->green("DS a néant") . "\n";
                    continue;
                } catch (Exception $e) {
                    echo $this->error_term . " Exception au save : $ds->_id : " . $e->getMessage() . "\n";
                }
            } else {
                $en_erreur = false;
                foreach ($ds_csv as $ds_csv_ligne) {
                    $ds = $this->importProduitInDS($ds, str_getcsv($ds_csv_ligne, ','));
                    if (is_null($ds)) {
                        $en_erreur = true;
                        break;
                    }
                }
                if ($en_erreur) {
                    continue;
                }
                $this->checkVolumesDS($ds, $ds_csv_datas);
                try {
                    $ds->declaration->cleanAllNodes();
                    $ds->update();
                    $ds->save();
                    echo "La DS " . $this->green($ds->_id) . " a été sauvée sans encombre.\n";
                } catch (Exception $e) {
                    echo $this->error_term . " Exception au save : $ds->_id : " . $e->getMessage() . "\n";
                }
            }
        }
    }

    public function setConf()
    {
        $this->conf = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');        
    }

    public function getConf()
    {
        if (!$this->conf)
            $this->setConf();
        return $this->conf;
    }
    
    public function setCurrent()
    {
        $currentClient = acCouchdbManager::getClient('Current');
        $this->current =  $currentClient::getCurrent();
    }

    public function getCurrent()
    {
        if (!$this->current)
            $this->setCurrent();
        return $this->current;
    }
    
    
    protected function importProduitInDS($ds, $productRow)
    {
        $hash = $this->constructHash($productRow, $ds->_id);
        if ($hash) {
            $detail = $ds->addNoeud($hash);
            $id_ds_import = $productRow[self::CSV_DS_ID];

            $vol_normal = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_STOCK]);
            $vol_vt = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_VT]);
            $vol_sgn = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_SGN]);

            $detail = $ds->addVolumesWithHash($hash, $productRow[self::CSV_PRODUIT_LIEUDIT], $vol_normal, $vol_vt, $vol_sgn, true);

            if (is_string($detail) && $detail == "NO_CEPAGE") {
                echo $this->error_term . " le cepage $hash de la DS $id_ds_import n'a pas été trouvé \n";
                return null;
            }
            if (is_string($detail) && $detail == "NO_VTSGN_AND_VTORSGN") {
                echo $this->error_term . " le cepage $hash de la DS $id_ds_import est notifié comme no_vtsgn(1) et présente du volume vt ou sgn non nulle \n";
                return null;
            }
        } else {
            return null;
        }
        return $ds;
    }

    protected function constructHash($productRow, $id_ds = null )
    {
        $conf = $this->getConf();
        switch ($productRow[self::CSV_PRODUIT_APPELLATION]) {
            case 1:
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'VT') {
                    $couleur_node = 'cepage_' . $productRow[self::CSV_PRODUIT_COULEUR];
                    return $conf->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->$couleur_node->getHash();
                }

                if (($productRow[self::CSV_PRODUIT_CEPAGE] == 'PR') || ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PN' && $productRow[self::CSV_PRODUIT_COULEUR] == 'RG')) {
                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                    return $conf->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->cepage_PR->getHash();
                }
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PN' && $productRow[self::CSV_PRODUIT_COULEUR] == 'RS') {
                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                    return $conf->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->$cepage_node->getHash();
                }

//                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PN' && $productRow[self::CSV_PRODUIT_COULEUR] == 'RG') {
//                   $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];      
//                   return $conf->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->$cepage_node->getHash();
//                }            
//                if (($productRow[self::CSV_PRODUIT_CEPAGE] == 'PR') || ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PN' && $productRow[self::CSV_PRODUIT_COULEUR] == 'RS')) {
//                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
//                    return $conf->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->cepage_PR->getHash();
//                }
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'KL') {
                    $id = $productRow[self::CSV_DS_ID];
                    // echo $this->warning_term . "Gestion de l'exception lié au cepage KL (COMMUNALE) dans la DS $id \n";
                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                    return $conf->recolte->certification->genre->appellation_COMMUNALE->mention->lieuKLEV->couleurBlanc->$cepage_node->getHash();
                }
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                return $conf->recolte->certification->genre->appellation_ALSACEBLANC->mention->lieu->couleur->$cepage_node->getHash();

            case 2:
                $id = $productRow[self::CSV_DS_ID];
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                // echo $this->warning_term . "Gestion automatique du cépage du crémant placé en $cepage_node pour la ds $id\n";
                return $conf->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->$cepage_node->getHash(); //$cepage_node->getHash();

            case 3:
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                $lieu_node = 'lieu' . $productRow[self::CSV_PRODUIT_LIEUDIT];
                if (!$conf->recolte->certification->genre->appellation_GRDCRU->mention->exist($lieu_node)) {
                    if($this->ds_negoce){
                        echo $this->warning_term . " Le Lieu $lieu_node n'est pas dans la conf grands crus => il a été remplacé par lieu01 A MODIFIE : $id_ds\n";                    
                        return $conf->recolte->certification->genre->appellation_GRDCRU->mention->lieu01->couleur->$cepage_node->getHash();
                    }
                    echo $this->error_term . " Le Lieu $lieu_node n'existe pas dans la conf pour les grands crus\n";
                    return null;
                }
                if (!$conf->recolte->certification->genre->appellation_GRDCRU->mention->$lieu_node->couleur->exist($cepage_node)) {
                    echo $this->error_term . " Le Cépage $cepage_node n'existe pas dans la conf pour les grands crus pour le lieu $lieu_node \n";
                    return null;
                }
                
                return $conf->recolte->certification->genre->appellation_GRDCRU->mention->$lieu_node->couleur->$cepage_node->getHash();

            case 7:
                echo " **** Integration d'une DS contenant des communale *** \n";
                $lieu_node = 'lieu' . $productRow[self::CSV_PRODUIT_LIEUDIT];
                $couleur_node = 'couleur' . $this->getCouleur($productRow[self::CSV_PRODUIT_COULEUR]);
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                return $conf->recolte->certification->genre->appellation_COMMUNALE->mention->$lieu_node->$couleur_node->$cepage_node->getHash();

            case 8:
                echo " **** Integration d'une DS contenant des Lieux dit *** \n";
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                $couleur_node = 'couleur' . $this->getCouleur($productRow[self::CSV_PRODUIT_COULEUR]);
                return $conf->recolte->certification->genre->appellation_LIEUDIT->mention->lieu->couleur->$cepage_node->getHash();


            default:
                $appellation_num = $productRow[self::CSV_PRODUIT_APPELLATION];
                $this->logLigne($this->error_term, "Le numéro d'appellation $appellation_num n'a pas été trouvé.", $productRow, $productRow[self::CSV_DS_ID], ',');
                return null;
        }
    }

    protected function checkVolumesDS($ds, $productRow)
    {
        $vinTable = 0;
        if ($ds->declaration->hasVinTable()) {
            $vinTable = $ds->declaration->getVinTable()->total_stock;
        }
        $vol_normal = $ds->declaration->certification->total_normal - $vinTable;
        if (round($vol_normal, 2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_STOCK])) {
            // echo " #" . $this->green(" vol_normal: " . $productRow[self::CSV_DS_VOLUME_TOTAL_STOCK]) . " ";
        } else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_normal . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_STOCK]) . "\n";

        if (round($ds->declaration->certification->total_sgn, 2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_SGN])) {
            // echo " #" . $this->green(" vol_sgn: " . $productRow[self::CSV_DS_VOLUME_TOTAL_SGN]) . " ";
        } else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_sgn . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_SGN]) . "\n";

        if (round($ds->declaration->certification->total_vt, 2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_VT])) {
            // echo " #" . $this->green(" vol_vt: " . $productRow[self::CSV_DS_VOLUME_TOTAL_VT]) . " ";
        } else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_vt . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_VT]) . "\n";

        if ($vinTable) {
            echo " # DS possèdant du " . $this->green(" Vin De Table vol: " . $vinTable) . " \n";
        }
    }

    protected function importVinSansIg($ligne_ds, $ds)
    {
        $vinSansIgVolume = $this->convertToFloat($ligne_ds[self::CSV_DS_VOLUME_VINTABLE]);
        if ($vinSansIgVolume > 0) {
            $hash_vinSansIg = $this->getConf()->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->cepage_VINTABLE->getHash();
            $detail = $ds->addNoeud($hash_vinSansIg);
            $detail = $ds->addVolumesWithHash($hash_vinSansIg, null, $vinSansIgVolume, 0, 0, true);
        }
    }

    protected function createLieuxStockageForDS($dss)
    {
        $ds_client = DSCivaClient::getInstance();
        $tiersListIDs = array();
        
        foreach ($dss as $id_ds => $ds_csv) {
            $ds_ligne = $ds_csv[0];
            $ds_csv_datas = str_getcsv($ds_ligne, ',');
            $identifiant = $ds_csv_datas[self::CSV_DS_TCIVAB_CIVAGEN];
            if (!$identifiant || $identifiant == "") {
                echo $this->error_term . " Le Numéro $identifiant ne correspond pas à un numéro CIVA \n";
                return null;
            }
            $tiers = acCouchdbManager::getClient('_Tiers')->findByIdentifiantNegoce($identifiant);

            if (is_null($tiers)) {
                echo $this->error_term . " Le tiers d'identifiant $identifiant n'a pas été trouvé \n";
                return null;
            }
            if (!$tiers->isAjoutLieuxDeStockage()) {
                echo $this->error_term . " Le tiers d'identifiant $identifiant n'a pas le droit d'ajouter des lieux de stockage \n";
                continue;
            }
            $lieu_stockage = null;
            $adresse_lieu = $ds_csv_datas[self::CSV_DS_LIEUDESTOCKAGE];
            $is_lieu_principal = ($ds_csv_datas[self::CSV_DS_LIEU_PRINCIPAL] == "P");
            $lieux_existant = $tiers->getLieuxStockage();
            
            if ($is_lieu_principal) {
                $lieu_principal_existant = $tiers->getLieuStockagePrincipal();
                
                $adresse_lieu = $tiers->siege->adresse;  
                $commune = $tiers->siege->commune;
                $code_postal = $tiers->siege->code_postal;
                
                if($lieu_principal_existant){
                    $lieu_stockage = $lieu_principal_existant;
                    if ($adresse_lieu != "") {
                        $tiers->lieux_stockage->{$lieu_stockage->numero}->adresse = $adresse_lieu;
                    }                
                }
            } else {
                if ($adresse_lieu == "") {
                    echo $this->warning_term . " L'adresse du lieu secondaire du tiers d'identifiant $identifiant n'est pas spécifié dans le CSV \n";
                    $adresse_lieu = $tiers->siege->adresse;
                }
                if(count($lieux_existant) > 1){
                        foreach ($lieux_existant as $lieu_existant) {
                            $num = substr($lieu_existant->numero, 10);
                            if($num > 1 && $lieu_existant->adresse == $adresse_lieu){
                                $lieu_stockage = $lieu_existant;  
                                break;
                            }
                        }
                }
                $commune = "";
                $code_postal = "";
            }
            
            if(!$lieu_stockage){
                $lieu_stockage = $tiers->storeLieuStockage($adresse_lieu, $commune, $code_postal);
            }
            if ($is_lieu_principal) {
                echo "Lieu principal crée " . $this->displayLieuStockage($lieu_stockage) . "\n";
            } else {
                echo $this->warning_term . " lieu secondaire crée " . $this->displayLieuStockage($lieu_stockage) . "\n";
            }
            $tiers->save();
        }
    }

    public function displayLieuStockage($lieu_stockage)
    {
        return "(" . $lieu_stockage->numero . ") " . $lieu_stockage->nom . " " . $lieu_stockage->adresse . " " . $lieu_stockage->code_postal . " " . $lieu_stockage->commune;
    }

}
