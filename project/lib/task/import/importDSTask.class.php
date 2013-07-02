<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class importDSTask
 * @author mathurin
 */
class importDSTask extends importAbstractTask {

    protected $conf = null;
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

    protected function configure() {
        // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('file', sfCommandArgument::REQUIRED, "Fichier csv pour l'import"),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
                // add your own options here
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

    protected function execute($arguments = array(), $options = array()) {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        set_time_limit(0);
        $file = file($arguments['file']);
        echo "\n*** Transformation des CSV en tableau de DS ***";
        $dss = $this->transformFileInDSS($file);
        $dss = $this->sortDSSArrayRows($dss);
        echo "\n*** Import des DS ***\n";
        $this->importDSS($dss);
        echo "\n*** FIN DE l'Import des DS ***\n";
    }

    protected function transformFileInDSS($file) {
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

    protected function sortDSSArrayRows($dss) {
        foreach ($dss as $key => $ds) {
            ksort($ds, SORT_NUMERIC);
            $dss[$key] = array_values($ds);
        }
        return $dss;
    }

    protected function importDSS($dss) {

        $ds_client = DSCivaClient::getInstance();

        foreach ($dss as $id_ds => $ds_csv) {
            $ds = new DSCiva();
            if (!count($ds_csv))
                throw new sfException("La ligne d'identifiant $id_ds n'est pas valide.");
            $ds_ligne = $ds_csv[0];
            $ds_csv_datas = str_getcsv($ds_ligne, ',');

            $date = date_format($this->convertToDateObject($ds_csv_datas[self::CSV_DS_DATE_SAISIE]), 'Y-m-d');
            $periode = $ds_client->buildPeriode($date);
            $ds->date_emission = $date;
            $ds->date_stock = $date;
            $ds->numero_archive = substr($ds_csv_datas[self::CSV_DS_ID],2);
            $ds->identifiant = $ds_csv_datas[self::CSV_DS_CVI];
            $num_lieu = ($ds_csv_datas[self::CSV_DS_LIEU_PRINCIPAL] == "P") ? '001' : $ds_client->getNextLieuStockageByCviAndDate($ds->identifiant, $date);
            $ds->_id = sprintf('DS-%s-%s-%s', $ds->identifiant, $periode, $num_lieu);
            try {
                $ds->storeInfos();
            } catch (sfException $e) {
//                if (count($ds_csv) == 1 && count($ds_csv_datas) < 25) {
//                    echo "--------> La DS " . $this->green($ds->_id) . " n'est pas sauvée, c'est une " . $this->green("DS a néant") . "\n";
//                }
//                else
//                {
                    echo $this->error_term . " pour la DS $id_ds : " . $e->getMessage() . "\n";
                    //echo $this->error_term . " pour la DS $id_ds (qui n'est pas néant) => $ds->identifiant n'a pas de lieux de stockage \n";
//                }
                continue;
            }


            //    Autres
            if($ds->isDsPrincipale()){
                $rebeche = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_REBECHES]);
                $dplc = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_DPLC]);
                $mouts = $this->convertToFloat($ds_csv_datas[self::CSV_DS_VOLUME_MOUTS]);
                $ds->updateAutre($rebeche,$dplc,0,$mouts);
            }
            
            //  Etape
            if($this->convertOuiNon($ds_csv_datas[self::CSV_DS_TRAITEE])){
                $ds->add('num_etape', 6);
                $ds->validate($ds->date_emission);
            }
            else
                $ds->add('num_etape', 5);
            // Produits

            if (count($ds_csv) == 1 && count($ds_csv_datas) < 25) {
                    $ds->save();
                    echo "La DS " . $this->green($ds->_id) . " a été sauvée et est une " . $this->green("DS a néant") . "\n";
                continue;
            } else {
                $en_erreur = false;
                foreach ($ds_csv as $ds_csv_ligne) {
                    $ds = $this->importProduitInDS($ds, str_getcsv($ds_csv_ligne, ','));
                    if(is_null($ds))
                    {
                        $en_erreur = true;
                        break;
                    }
                }
                if($en_erreur){
                    continue;
                }
                $this->checkVolumesDS($ds, $ds_csv_datas);
                $ds->save();
                echo "La DS " . $this->green($ds->_id) . " a été sauvée sans encombre.\n";
            }
        }
    }

    public function setConf() {
        $this->conf = ConfigurationClient::getConfiguration();
    }

    public function getConf() {
        if (!$this->conf)
            $this->setConf();
        return $this->conf;
    }

    protected function importProduitInDS($ds, $productRow) {
        $hash = $this->constructHash($productRow);
        if ($hash) {
            $detail = $ds->addNoeud($hash);
            $id_ds_import = $productRow[self::CSV_DS_ID];

            $vol_normal = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_STOCK]);
            $vol_vt = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_VT]);
            $vol_sgn = $this->convertToFloat($productRow[self::CSV_PRODUIT_VOLUME_SGN]);

            $detail = $ds->addVolumesWithHash($hash, $productRow[self::CSV_PRODUIT_LIEUDIT], $vol_normal, $vol_vt, $vol_sgn,true);

            if (is_string($detail) && $detail == "NO_CEPAGE"){
                echo $this->error_term . " le cepage $hash de la DS $id_ds_import n'a pas été trouvé \n";
                return null;
            }
            if (is_string($detail) && $detail == "NO_VTSGN_AND_VTORSGN"){
                echo $this->error_term . " le cepage $hash de la DS $id_ds_import est notifié comme no_vtsgn(1) et présente du volume vt ou sgn non nulle \n";
                return null;
            }
        }
        else
        {
            return null;
        }
        return $ds;
    }


    protected function constructHash($productRow) {
        $conf = $this->getConf();
        switch ($productRow[self::CSV_PRODUIT_APPELLATION]) {
            case 1:
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'VT') {
                    $couleur_node = 'cepage_' . $productRow[self::CSV_PRODUIT_COULEUR];
                    return $conf->recolte->certification->genre->appellation_VINTABLE->mention->lieu->couleur->$couleur_node->getHash();
                }
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PN') {
                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                    return $conf->recolte->certification->genre->appellation_PINOTNOIR->mention->lieu->couleur->$cepage_node->getHash();
                }
                if ($productRow[self::CSV_PRODUIT_CEPAGE] == 'PR') {
                    $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                    return $conf->recolte->certification->genre->appellation_PINOTNOIRROUGE->mention->lieu->couleur->$cepage_node->getHash();
                }
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
                $cepage_node = 'cepage_'.$productRow[self::CSV_PRODUIT_CEPAGE];
               // echo $this->warning_term . "Gestion automatique du cépage du crémant placé en $cepage_node pour la ds $id\n";
                return $conf->recolte->certification->genre->appellation_CREMANT->mention->lieu->couleur->$cepage_node->getHash(); //$cepage_node->getHash();
                
            case 3:
                $cepage_node = 'cepage_' . $productRow[self::CSV_PRODUIT_CEPAGE];
                $lieu_node = 'lieu' . $productRow[self::CSV_PRODUIT_LIEUDIT];
                if (!$conf->recolte->certification->genre->appellation_GRDCRU->mention->exist($lieu_node)) {
                    echo $this->error_term . " Le Lieu $lieu_node n'existe pas dans la conf pour les grands crus\n";
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

    protected function checkVolumesDS($ds, $productRow) {
        if (round($ds->declaration->certification->total_normal,2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_STOCK])){
           // echo " #" . $this->green(" vol_normal: " . $productRow[self::CSV_DS_VOLUME_TOTAL_STOCK]) . " ";
        }
        else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_normal . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_STOCK])."\n";

        if (round($ds->declaration->certification->total_sgn,2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_SGN])){
           // echo " #" . $this->green(" vol_sgn: " . $productRow[self::CSV_DS_VOLUME_TOTAL_SGN]) . " ";
        }
        else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_sgn . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_SGN])."\n";

        if (round($ds->declaration->certification->total_vt,2) == $this->convertToFloat($productRow[self::CSV_DS_VOLUME_TOTAL_VT])){
           // echo " #" . $this->green(" vol_vt: " . $productRow[self::CSV_DS_VOLUME_TOTAL_VT]) . " ";
        }
        else
            echo " #" . $this->yellow(" " . $ds->declaration->certification->total_vt . "!=" . $productRow[self::CSV_DS_VOLUME_TOTAL_VT])."\n";
    }

}
