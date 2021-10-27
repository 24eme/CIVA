<?php

class ExportDSCiva {

    protected $periode;
    protected $campagne;
    protected $ds_ids;
    protected $client_ds;
    protected $ds_liste;
    protected $etablissementsDb2;

    const CSV_DS_TRAITEE = 22; // "O" ou "N"
    const CSV_DS_DATE_SAISIE = 23; // JJMMAAAA
    const CODE_DOUANE_ED = "1B001S 9";

    public function __construct($periode, $types_ds = array(DSCivaClient::TYPE_DS_PROPRIETE, DSCivaClient::TYPE_DS_NEGOCE)) {
        if (!preg_match('/^[0-9]{6}$/', $periode)) {
            throw new sfException("La période doit être au format yyyymm ($periode)");
        }
        $this->periode = $periode;
        $this->campagne = substr($periode, 0, 4);
        $this->client_ds = DSCivaClient::getInstance();
        $this->ds_ids = $this->client_ds->getAllIdsByPeriode($this->periode, $types_ds);
        $this->ds_liste = array();
        foreach ($this->ds_ids as $ds_id) {
            $ds = $this->client_ds->find($ds_id);
            if (!$ds) {
              continue;
            }
            $ds_principale = $this->client_ds->getDSPrincipaleByDs($ds);
            if (preg_match('/^C?(67|68)/', $ds->identifiant) && $ds_principale->isValidee()) {
                $this->ds_liste[$ds_principale->_id] = $ds_principale;
                $this->ds_liste[$ds->_id] = $ds;
            }
        }
        $tiersCsv = new Db2Tiers2Csv(sfConfig::get('sf_root_dir')."/data/import/Tiers/Tiers-last");
        $this->etablissementsDb2 = $tiersCsv->getEtablissements();
    }

    public function getDSListe() {
        return $this->ds_liste;
    }

    public function getDSIdListe() {
        return $this->ds_ids;
    }

    public function getDSNonValideesListe() {
        $result = array();
        foreach ($this->ds_ids as $ds_id) {
            $ds = $this->client_ds->find($ds_id);
            if (preg_match('/^C?(67|68)/', $ds->identifiant) && $ds->isDsPrincipale() && !$ds->isValidee()) {
                $result[] = $this->client_ds->find($ds_id);
            }
        }
        return $result;
    }

    protected function createProduitsAgregat($products, $vtsgn = false, $xml = false) {
        $resultAgregat = array();
        foreach ($products as $app_produit => $produit) {
            $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $app_produit);
            if ($appellation_key == 'VINTABLE' && !$xml){
               continue;
            }
            if (!$vtsgn || $produit->hasVtsgn()) {
                $code_douane = $this->getCodeDouane($produit,'',$xml);
                if (!array_key_exists($code_douane, $resultAgregat)) {
                    $resultAgregat[$code_douane] = $this->initProduitAgregat();
                }
                $this->setProduitAgregat($resultAgregat[$code_douane], $this->convertToFloat($produit->total_stock, false), $produit->getHash(), $produit, $this->convertToFloat($produit->total_normal, false), $this->convertToFloat($produit->total_vt, false), $this->convertToFloat($produit->total_sgn, false));
            } else {

                $volume_vt = $produit->total_vt;
                if ($volume_vt > 0) {
                    $code_douane = $this->getCodeDouane($produit, 'VT');
                    if (!array_key_exists($code_douane, $resultAgregat)) {
                        $resultAgregat[$code_douane] = $this->initProduitAgregat();
                    }
                    $this->setProduitAgregat($resultAgregat[$code_douane], $this->convertToFloat($volume_vt, false), $produit->getHash(), $produit);
                }

                $volume_sgn = $produit->total_sgn;
                if ($volume_sgn > 0) {
                    $code_douane = $this->getCodeDouane($produit, 'SGN');
                    if (!array_key_exists($code_douane, $resultAgregat)) {
                        $resultAgregat[$code_douane] = $this->initProduitAgregat();
                    }
                    $this->setProduitAgregat($resultAgregat[$code_douane], $this->convertToFloat($volume_sgn, false), $produit->getHash(), $produit);
                }

                $volume_normal = $produit->total_normal;
                if ($volume_normal > 0) {
                    $code_douane = $this->getCodeDouane($produit,'',$xml);
                    if (!array_key_exists($code_douane, $resultAgregat)) {
                        $resultAgregat[$code_douane] = $this->initProduitAgregat();
                    }
                    $this->setProduitAgregat($resultAgregat[$code_douane], $this->convertToFloat($volume_normal, false), $produit->getHash(), $produit);
                }
            }
        }
        return $resultAgregat;
    }

    public function exportEntete() {
        $entete_string = "";
        foreach ($this->ds_liste as $cpt => $ds) {
            $entete_string.= $this->makeEnteteLigne($ds);
            if ($cpt < (count($this->ds_liste) - 1))
                $entete_string.= "\r\n";
        }
        return $entete_string;
    }

    public function exportLigne() {
        $ligne_string = "";
        foreach ($this->ds_liste as $cpt => $ds) {
            $ligne_string.= $this->makeLignes($ds);
        }
        return $ligne_string;
    }

    protected function makeEnteteLigne($ds) {
        $id_csv = substr($this->campagne, 2) . $ds->numero_archive;
//        $neant = ($ds->isDsPrincipale() && $ds->isDsNeant()) ? "\"N\"" : "\"P\"";
        $etb = $ds->getEtablissementObject();

        $lieu_stockage = "";
        if ($ds->stockage->exist("adresse")) {
            $lieu_stockage = str_replace(',', '', $ds->stockage->adresse);
        } elseif ($ds->declarant->exist("adresse")) {
            $lieu_stockage = str_replace(',', '', $ds->declarant->adresse);
        }

        $principale = ($ds->isDsPrincipale()) ? "\"P\"" : "\"S\"";
        $proprieteNegoce = ($ds->isTypeDsNegoce())? "\"N\"" : "\"P\"";
        $num_db2 = $this->etablissementsDb2[$etb->_id][EtablissementCsvFile::CSV_NUM_REPRISE];
        $cvi = "\"" . $ds->identifiant . "\"";
        $civagene0 = "0"; // A trouvé


        $row = $this->campagne . "," . $id_csv . ",".$proprieteNegoce."," . $lieu_stockage . "\"," . $principale . "," . $num_db2 . "," . $cvi . "," . $civagene0 . ",";

//VINTABLE
        $vin_table_volume = 0;
        if ($ds->exist('declaration') && $ds->declaration->hasVinTable()) {
            $vin_table = $ds->declaration->getVinTable();
            if (($vin_table->total_vt && $vin_table->total_vt > 0) ||
                    ($vin_table->total_sgn && $vin_table->total_sgn > 0)) {
                throw new sfException("L'appellation vin de table contient du vt ou du sgn pour : " . $ds->_id . " ?");
            }
            $vin_table_volume = $vin_table->total_normal;
        }

        $vci_volume = 0;
        if ($ds->exist('declaration/certification/genreVCI')) {
            $vci_volume = $ds->get('declaration/certification/genreVCI')->total_normal;
        }

// Alsace Blanc + PinotNOIR + PinotNOIRROUGE
        $stock_aoc_normal = 0;
        $stock_aoc_vt = 0;
        $stock_aoc_sgn = 0;
        if ($ds->exist('declaration') && $ds->declaration->hasAlsaceBlanc()) {
            $alsaceBlanc = $ds->declaration->getAlsaceBlanc();
            $stock_aoc_normal+= ($alsaceBlanc->total_normal) ? $alsaceBlanc->total_normal : 0;
            $stock_aoc_vt += ($alsaceBlanc->total_vt) ? $alsaceBlanc->total_vt : 0;
            $stock_aoc_sgn += ($alsaceBlanc->total_sgn) ? $alsaceBlanc->total_sgn : 0;
        }

        if ($ds->exist('declaration') && $ds->declaration->hasCommunale()) {
            $communale = $ds->declaration->getCommunale();
            $stock_aoc_normal+= ($communale->total_normal) ? $communale->total_normal : 0;
            $stock_aoc_vt += ($communale->total_vt) ? $communale->total_vt : 0;
            $stock_aoc_sgn += ($communale->total_sgn) ? $communale->total_sgn : 0;
        }

        if ($ds->exist('declaration') && $ds->declaration->hasLieuDit()) {
            $lieuDits = $ds->declaration->getLieuDit();
            $stock_aoc_normal+= ($lieuDits->total_normal) ? $lieuDits->total_normal : 0;
            $stock_aoc_vt += ($lieuDits->total_vt) ? $lieuDits->total_vt : 0;
            $stock_aoc_sgn += ($lieuDits->total_sgn) ? $lieuDits->total_sgn : 0;
        }

        if ($ds->exist('declaration') && $ds->declaration->hasPinotNoir()) {
            $pinotNoir = $ds->declaration->getPinotNoir();
            $stock_aoc_normal+= ($pinotNoir->total_normal) ? $pinotNoir->total_normal : 0;
            $stock_aoc_vt += ($pinotNoir->total_vt) ? $pinotNoir->total_vt : 0;
            $stock_aoc_sgn += ($pinotNoir->total_sgn) ? $pinotNoir->total_sgn : 0;
        }

        if ($ds->exist('declaration') && $ds->declaration->hasPinotNoirRouge()) {
            $pinotNoirRouge = $ds->declaration->getPinotNoirRouge();
            $stock_aoc_normal+= ($pinotNoirRouge->total_normal) ? $pinotNoirRouge->total_normal : 0;
            $stock_aoc_vt += ($pinotNoirRouge->total_vt) ? $pinotNoirRouge->total_vt : 0;
            $stock_aoc_sgn += ($pinotNoirRouge->total_sgn) ? $pinotNoirRouge->total_sgn : 0;
        }

        $row .= $this->convertToFloat($stock_aoc_normal) . "," . $this->convertToFloat($stock_aoc_vt) . "," . $this->convertToFloat($stock_aoc_sgn) . ",";


//GRAND CRU
        if ($ds->exist('declaration') && $ds->declaration->hasGrdCru()) {
            $grdCrus = $ds->declaration->getGrdCru();
            $row .= $this->convertToFloat($grdCrus->total_normal) . "," . $this->convertToFloat($grdCrus->total_vt) . "," . $this->convertToFloat($grdCrus->total_sgn) . ",";
        } else {
            $row .= ".00,.00,.00,";
        }

//CREMANT
        if ($ds->exist('declaration') && $ds->declaration->hasCremant()) {
            $cremant = $ds->declaration->getCremant();
            if (($cremant->total_vt && $cremant->total_vt > 0) ||
                    ($cremant->total_sgn && $cremant->total_sgn > 0)) {
                throw new sfException("L'appellation crémant contient du vt ou du sgn pour : " . $ds->_id . " ?");
            }
            $row .= $this->convertToFloat($cremant->total_normal) . ",";
        } else {
            $row .= ".00,";
        }

        if ($ds->exist('declaration') && $ds->declaration->exist('certification')) {
            $certif = $ds->declaration->certification;
            $row .= $this->convertToFloat($certif->total_normal - $vin_table_volume - $vci_volume) . "," . $this->convertToFloat($certif->total_vt) . "," . $this->convertToFloat($certif->total_sgn) . ",";
        } else {
            $row .= ".00,.00,.00,";
        }

//VINTABLE
        if ($vin_table_volume > 0) {
            $row .= $this->convertToFloat($vin_table_volume) . ",";
        } else {
            $row .= ".00,";
        }

        $date = $this->getDateForExport($ds);

        $row .= $this->convertToFloat($ds->mouts) . ",";
        $row .= $this->convertToFloat($ds->dplc) . ",";
        $row .= $this->convertToFloat($ds->rebeches) . ",";
        $row .= (DSCivaClient::getInstance()->getDSPrincipaleByDs($ds)->isDateDepotMairie()) ? "\"N\"" : "\"O\"";
        $row .= "," . $date;

        return $row;
    }

    protected function makeLignes($ds) {
        $cpt = 1;
        $row = "";
        $produitsAgreges = $this->getProduitsAgregesForDS($ds);

        $id_csv = substr($this->campagne, 2) . $ds->numero_archive;

       foreach ($produitsAgreges as $code_douane => $obj) {
            $app_produit = $obj->hash;
            $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $app_produit);
            $cepage_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z]+)cepage_([A-Z]{2})/', '$4', $app_produit);

            if($code_douane == "1R001S "){
                    $row.= $id_csv . ",";
                    $row.= "1,\"PN\",\"RG\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    $cpt++;
                    continue;
            }

            if($code_douane == "1S001S "){
                    $row.= $id_csv . ",";
                    $row.= "1,\"PN\",\"RS\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    $cpt++;
                    continue;
            }

            if($code_douane == "1B001S99") {
                continue;
            }

            if($code_douane == "1B001M90") {
                continue;
            }

            switch ($appellation_key) {
//                case 'PINOTNOIR':
//                    $row.= $id_csv . ",";
//                    $row.= "1,\"PN\",\"RS\",\"\"," . $cpt . ",";
//                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
//                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
//                    $row.="\r\n";
//                    break;
//                case 'PINOTNOIRROUGE':
//                    $row.= $id_csv . ",";
//                    $row.= "1,\"PN\",\"RG\",\"\"," . $cpt . ",";
//                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
//                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
//                    $row.="\r\n";
//                    break;
                case 'CREMANT':
                    $row.= $id_csv . ",";
                    $row.= "2,\"" . $cepage_key . "\",\"" . $cepage_key . "\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    break;
                case 'GRDCRU':
                    $lieu = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)([\/a-zA-Z]+)\/lieu([A-Za-z0-9]{2})\/([\/a-zA-Z_-]+)/', '$4', $app_produit);
                    $row.= $id_csv . ",";
                    $row.= "3,\"" . $cepage_key . "\",\"BL\",\"" . $lieu . "\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    break;
                case 'ALSACEBLANC':
                    $row.= $id_csv . ",";
                    $row.= "1,\"" . $cepage_key . "\",\"BL\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    break;

                case 'COMMUNALE':
                    $row.= $id_csv . ",";
                    $lieu = preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu([A-Z]{4})([\/a-zA-Z_-]+)/', '$4', $app_produit);
                    $couleur = $this->getCouleurForExport(preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu([A-Z]{4})\/couleur([A-Za-z]+)\/([\/a-zA-Z_-]+)/', '$5', $app_produit));

                    if ($lieu == 'KLEV') {
                        $row.= "1,\"KL\",\"BL\",\"\"," . $cpt . ",";
                    } else {
                        $row.= "1,\"" . $cepage_key . "\",\"" . $couleur . "\",\"\"," . $cpt . ",";
                    }
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    break;

                case 'LIEUDIT':
                    $row.= $id_csv . ",";
                    $couleur = $this->getCouleurForExport(preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu\/couleur([A-Za-z]+)\/([\/a-zA-Z_-]+)/', '$4', $app_produit));
                    $row.= "1,\"" . $cepage_key . "\",\"" . $couleur . "\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($obj->volume_normal) . "," . $this->convertToFloat($obj->volume_vt) . ",";
                    $row.= $this->convertToFloat($obj->volume_sgn) . "," . $this->convertToFloat($obj->volume);
                    $row.="\r\n";
                    break;
            }
            $cpt++;
        }

        return $row;
    }

    protected function convertToFloat($vol, $withTrunc = true) {
        if (!$vol)
            return ($withTrunc) ? ".00" : "0.00";
        $result = sprintf("%01.02f", round(str_replace(",", ".", $vol) * 1, 2));
        if ($withTrunc && $vol < 1)
            return substr($result, 1);
        return $result;
    }

    protected function getDateForExport($ds) {
        if ($ds->exist('modifiee')) {
            $dateArr = array();
            if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $ds->modifiee, $dateArr)) {
                $a = $dateArr[1];
                $m = $dateArr[2];
                $j = $dateArr[3];
            }
            return ($j * 1) . $m . $a;
        }
        if ($ds->exist('validee')) {
            $dateArr = array();
            if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $ds->validee, $dateArr)) {
                $a = $dateArr[1];
                $m = $dateArr[2];
                $j = $dateArr[3];
            }
            return ($j * 1) . $m . $a;
        }
        return '';
    }

    protected function getCouleurForExport($couleur) {
        switch ($couleur) {
            case 'Blanc':
                return 'BL';
            case 'Rose':
                return 'RS';
            case 'Rouge':
                return 'RG';
            default:
                throw new sfException("La couleur $couleur n'est pas connue dans la configuration.");
        }
        return null;
    }

    public function getCodeDouane($cepage, $vtsgn = '',$xml=false) {
        $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $cepage->getHash());

        switch ($appellation_key) {
            case 'VINTABLE':
                if($xml){
                    $cepage_code = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)\/lieu([A-Z]*)\/couleur([A-Za-z]*)\/cepage_([A-Z]{2})/', '$6', $cepage->getHash());
                    if($cepage->getKey() == 'cepage_VINTABLE'){
                       return "4B999";
                    }
                    if($cepage->getKey() == 'cepage_MS'){
                       return "4B999M";
                    }
                }
                return null;
            case 'ALSACEBLANC':
                if ($cepage->getKey() == 'cepage_ED') {
                    return self::CODE_DOUANE_ED;
                }

                return $cepage->getConfig()->getCodeDouane($vtsgn);
                //return $cepage->getConfig()->getDouane()->getFullAppCode($vtsgn) . $cepage->getConfig()->getDouane()->getCodeCepage();
            case 'LIEUDIT':
            case 'COMMUNALE':
                $cepage_code = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)\/lieu([A-Z]*)\/couleur([A-Za-z]*)\/cepage_([A-Z]{2})/', '$6', $cepage->getHash());
                if ($cepage->getKey() == 'cepage_ED') {
                    return self::CODE_DOUANE_ED;
                }
                if ($cepage_code == "KL") {

                    return $cepage->getConfig()->getCodeDouane($vtsgn);
                }
                if ($cepage_code == "PR") {
                    $hash = "/declaration/certification/genre/appellation_PINOTNOIRROUGE/mention/lieu/couleur/cepage_" . $cepage_code;
                    $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get(HashMapper::convert($hash));

                    return substr($config->getCodeDouane($vtsgn), 0, -1);
                }

                $hash = "/declaration/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_" . $cepage_code;
                $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get(HashMapper::convert($hash));

                return $config->getCodeDouane($vtsgn);

            case 'PINOTNOIR':
            case 'PINOTNOIRROUGE':

                return substr($cepage->getConfig()->getCodeDouane($vtsgn), 0, -1);

            default:

                return $cepage->getConfig()->getCodeDouane($vtsgn);
        }
    }

    protected function initProduitAgregat() {
        $result = new stdClass();
        $result->volume = 0;
        $result->volume_normal = 0;
        $result->volume_vt = 0;
        $result->volume_sgn = 0;
        $result->hash = null;
        $result->produit = null;
        return $result;
    }

    protected function setProduitAgregat(&$obj, $vol, $hash, $prod, $vol_normal = 0, $vol_vt = 0, $vol_sgn = 0) {
        $obj->volume += $vol;
        $obj->hash = $hash;
        $obj->produit = $prod;
        $obj->volume_normal += $vol_normal;
        $obj->volume_vt += $vol_vt;
        $obj->volume_sgn += $vol_sgn;
    }

    protected function getProduitsAgregesForDS($ds, $vtsgn = false, $xml = false) {
        $appelations_1 = array("\/appellation_ALSACEBLANC\/",
            "\/appellation_COMMUNALE\/",
            "\/appellation_LIEUDIT\/",
            "\/appellation_PINOTNOIR\/",
            "\/appellation_PINOTNOIRROUGE\/");
        $appelations_2 = array("\/appellation_GRDCRU\/",
            "\/appellation_CREMANT\/");
        $appelations_3 = array("\/appellation_VINTABLE\/");

        $produits = array_merge($ds->declaration->getProduitsSortedWithFilter($appelations_1), $ds->declaration->getProduitsSortedWithFilter($appelations_2));
        if($xml){
            $produits = array_merge($produits, $ds->declaration->getProduitsSortedWithFilter($appelations_3));
        }
        return $this->createProduitsAgregat($produits, $vtsgn, $xml);
    }

    protected function getAppellationNumero($appellation_key) {
        switch ($appellation_key) {
            case 'CREMANT':
                return 2;
            case 'GRDCRU':
                return 3;
            case 'PINOTNOIR':
            case 'PINOTNOIRROUGE':
            case 'ALSACEBLANC':
            case 'VINTABLE':
            case 'LIEUDIT':
            case 'COMMUNALE':
            default:
                return 1;
        }
        return 1;
    }

}
