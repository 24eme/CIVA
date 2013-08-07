<?php

class ExportDSCiva {

    protected $campagne;
    protected $ds_ids;
    protected $client_ds;
    protected $ds_liste;

    const CSV_DS_TRAITEE = 22; // "O" ou "N"
    const CSV_DS_DATE_SAISIE = 23; // JJMMAAAA

    public function __construct($campagne) {
        if (!preg_match('/^[0-9]{4}$/', $campagne)) {
            throw new sfException("La campagne doit être une année ($campagne)");
        }
        $this->campagne = $campagne;
        $this->client_ds = DSCivaClient::getInstance();
        $this->ds_ids = $this->client_ds->getAllIdsByCampagne($this->campagne);
        $this->ds_liste = array();
        foreach ($this->ds_ids as $ds_id) {
            $ds = $this->client_ds->find($ds_id);
            if (preg_match('/^(67|68)/', $ds->identifiant) && $this->client_ds->getDSPrincipaleByDs($ds)->isValidee()) {
                $this->ds_liste[] = $this->client_ds->find($ds_id);
            }
        }
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
            if (preg_match('/^(67|68)/', $ds->identifiant) && $ds->isDsPrincipale() && !$ds->isValidee()) {
                $result[] = $this->client_ds->find($ds_id);
            }
        }
        return $result;
    }

    public function exportXml() {
        $export_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" . "\r\n\r\n";
        $export_xml.="<listeDecStock>\r\n";
        $tab_cvi = array();
        $current_cvi = 0;
        foreach ($this->ds_liste as $cpt => $ds) {
            if (in_array($ds->declarant->cvi, $tab_cvi) && $ds->declarant->cvi == $current_cvi) {
                $export_xml.= $this->makeXMLDS($ds);
            } else {
                if (($ds->declarant->cvi != $current_cvi) && $current_cvi != 0) {
                    $export_xml.="\t</decStock>\r\n";
                }
                $current_cvi = $ds->declarant->cvi;
                $export_xml.="\t<decStock numCvi=\"" . $current_cvi . "\">\r\n";
                $export_xml.= $this->makeXMLDS($ds);
                $tab_cvi[] = $current_cvi;
            }
        }
        if(count($this->ds_liste)){
            $export_xml.="\t</decStock>\r\n";
        }
        $export_xml.="</listeDecStock>\r\n";
        return $export_xml;
    }

    protected function makeXMLDS($ds) {
        $lignes = "";
        if ($ds->isDsPrincipale()) {
            $lignes.="\t\t<volLie>" . $this->convertToFloat($ds->lies, false) . "</volLie>\r\n";
            $lignes.="\t\t<volDplc>" . $this->convertToFloat($ds->dplc, false) . "</volDplc>\r\n";
            $lignes.="\t\t<volVinNc>" . $this->convertToFloat(DSCivaClient::getInstance()->getTotalSansIG($ds), false) . "</volVinNc>\r\n";
        }

        $lieu_stockage = $ds->identifiant . $ds->getLieuStockage();
        $produitsAgreges = $this->createProduitsAgregatForXML($ds->declaration->getProduitsSorted());
        foreach ($produitsAgreges as $code_douane => $volume) {
            $lignes.= $this->makeXMLDSLigne($lieu_stockage, $code_douane, $volume);
        }
        $lignes .= $this->addXMLDSMouts($ds);
        return $lignes;
    }

    protected function makeXMLDSLigne($lieu_stockage, $code_douane, $volume) {
        $ligne = "\t\t<ligne>\r\n";
        $ligne .= "\t\t\t<codeInstallation>" . $lieu_stockage . "</codeInstallation>\r\n";
        $ligne .= "\t\t\t<codeProduit>" . $code_douane . "</codeProduit>\r\n";
        $ligne .= "\t\t\t<volume>" . $volume . "</volume>\r\n";
        $ligne .= "\t\t</ligne>\r\n";
        return $ligne;
    }

    protected function createProduitsAgregatForXML($products) {
        $resultAgregat = array();
        foreach ($products as $app_produit => $produit) {
            $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $app_produit);
            if ($appellation_key == 'VINTABLE')
                continue;
            if ($produit->hasVtsgn()) {
                $code_douane = $this->getCodeDouane($produit);
                if (!array_key_exists($code_douane,$resultAgregat)) {
                    $resultAgregat[$code_douane] = 0;
                }
                $resultAgregat[$code_douane] += $this->convertToFloat($produit->total_stock, false);
            } else {

                $volume_vt = $produit->total_vt;
                if ($volume_vt > 0) {
                    $code_douane = $this->getCodeDouane($produit,'VT');
                    if (!array_key_exists($code_douane,$resultAgregat)) {
                        $resultAgregat[$code_douane] = 0;
                    }
                    $resultAgregat[$code_douane] += $this->convertToFloat($volume_vt, false);
                }

                $volume_sgn = $produit->total_sgn;
                if ($volume_sgn > 0) {
                    $code_douane = $this->getCodeDouane($produit,'SGN');
                    if (!array_key_exists($code_douane,$resultAgregat)) {
                        $resultAgregat[$code_douane] = 0;
                    }
                    $resultAgregat[$code_douane] += $this->convertToFloat($volume_sgn, false);
                }

                $volume_normal = $produit->total_normal;
                if ($volume_normal > 0) {
                    $code_douane = $this->getCodeDouane($produit);
                    if (!array_key_exists($code_douane,$resultAgregat)) {
                        $resultAgregat[$code_douane] = 0;
                    }
                    $resultAgregat[$code_douane] += $this->convertToFloat($volume_normal, false);
                }
            }
        }
        return $resultAgregat;
    }

    protected function addXMLDSMouts($ds) {
        if (!$ds->hasMouts()) {
            return '';
        }
        $lieu_stockage = $ds->identifiant . $ds->getLieuStockage();
        return $this->makeXMLDSLigne($lieu_stockage, "MC", $ds->getMouts());
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
        $neant = ($ds->isDsPrincipale() && $ds->isDsNeant()) ? "\"N\"" : "\"P\"";
        $etb = $ds->getEtablissement();
        $lieu_stockage = "";
        if ($ds->declarant->exist("exploitant") && $ds->declarant->exploitant->exist("adresse")) {
            $lieu_stockage = $ds->declarant->exploitant->adresse;
        } elseif ($ds->declarant->exist("adresse")) {
            $lieu_stockage = $ds->declarant->adresse;
        }

        $principale = ($ds->isDsPrincipale()) ? "\"P\"" : "\"S\"";
        $num_db2 = ($etb->exist('db2') && $etb->db2->exist('num')) ? $etb->db2->num : '';
        $cvi = "\"" . $ds->identifiant . "\"";
        $civagene0 = "0"; // A trouvé


        $row = $this->campagne . "," . $id_csv . "," . $neant . ",\"" . $lieu_stockage . "\"," . $principale . "," . $num_db2 . "," . $cvi . "," . $civagene0 . ",";

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
            $row .= $this->convertToFloat($certif->total_normal - $vin_table_volume) . "," . $this->convertToFloat($certif->total_vt) . "," . $this->convertToFloat($certif->total_sgn) . ",";
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
        $row .= ($ds->isValidee()) ? "\"O\"" : "\"N\"";
        $row .= "," . $date;

        return $row;
    }

    protected function makeLignes($ds) {
        $cpt = 1;
        $row = "";
        foreach ($ds->declaration->getProduits() as $app_produit => $produit) {
            $id_csv = substr($this->campagne, 2) . $ds->numero_archive;

            $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $app_produit);
            $cepage_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z]+)cepage_([A-Z]{2})/', '$4', $app_produit);
            switch ($appellation_key) {
                case 'PINOTNOIR':
                    $row.= $id_csv . ",";
                    $row.= "1,\"" . $cepage_key . "\",\"RG\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                case 'PINOTNOIRROUGE':
                    $row.= $id_csv . ",";
                    $row.= "1,\"PN\",\"RS\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                case 'ALSACEBLANC':
                    $row.= $id_csv . ",";
                    $row.= "1,\"" . $cepage_key . "\",\"BL\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                case 'CREMANT':
                    $row.= $id_csv . ",";
                    $row.= "2,\"" . $cepage_key . "\",\"" . $cepage_key . "\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                case 'GRDCRU':
                    $lieu = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)([\/a-zA-Z]+)\/lieu([A-Za-z0-9]{2})\/([\/a-zA-Z_-]+)/', '$4', $app_produit);

                    $row.= $id_csv . ",";
                    $row.= "3,\"" . $cepage_key . "\",\"BL\",\"" . $lieu . "\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                case 'COMMUNALE':
                    $row.= $id_csv . ",";
                    $lieu = preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu([A-Z]{4})([\/a-zA-Z_-]+)/', '$4', $app_produit);
                    $couleur = $this->getCouleurForExport(preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu([A-Z]{4})\/couleur([A-Za-z]+)\/([\/a-zA-Z_-]+)/', '$5', $app_produit));

                    if ($lieu == 'KLEV') {
                        $row.= "1,\"KL\",\"BL\",\"\"," . $cpt . ",";
                    } else {
                        $row.= "2,\"" . $cepage_key . "\",\"" . $couleur . "\",\"" . $lieu . "\"," . $cpt . ",";
                    }
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";

                    break;

                case 'LIEUDIT':
                    $row.= $id_csv . ",";
                    $couleur = $this->getCouleurForExport(preg_replace('/^([\/a-zA-Z]+)appellation_([A-Z]+)([\/a-zA-Z]+)lieu\/couleur([A-Za-z]+)\/([\/a-zA-Z_-]+)/', '$4', $app_produit));
                    $row.= "2,\"" . $cepage_key . "\",\"" . $couleur . "\",\"\"," . $cpt . ",";
                    $row.= $this->convertToFloat($produit->total_normal) . "," . $this->convertToFloat($produit->total_vt) . ",";
                    $row.= $this->convertToFloat($produit->total_sgn) . "," . $this->convertToFloat($produit->total_stock);
                    $row.="\r\n";
                    break;

                default:
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
    
   public function getCodeDouane($cepage, $vtsgn = '') {       
     $appellation_key = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)/', '$2', $cepage->getHash());
     if($cepage->getKey() == 'cepage_ED'){
         return "1B001S";
     }
     switch ($appellation_key) {
         case 'VINTABLE':
         return null;
         case 'ALSACEBLANC':             
            return $cepage->getConfig()->getDouane()->getFullAppCode($vtsgn).$cepage->getConfig()->getDouane()->getCodeCepage();     
         case 'LIEUDIT':
         case 'COMMUNALE':
            $cepage_code = preg_replace('/^([\/a-zA-Z]+)\/appellation_([A-Z]+)\/([\/0-9a-zA-Z_\-]+)\/lieu([A-Z]*)\/couleur([A-Za-z]*)\/cepage_([A-Z]{2})/', '$6', $cepage->getHash());
            if($cepage_code == "KL"){
               $hash = str_replace('/declaration','/recolte',$cepage->getCouleur()->getHash());
               $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get($hash);
               return $config->getDouane()->getFullAppCode($vtsgn);      
            }
            if($cepage_code == "PR"){
                $hash = "/recolte/certification/genre/appellation_PINOTNOIRROUGE/mention/lieu/couleur/cepage_".$cepage_code;
                $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get($hash);
                return $config->getDouane()->getFullAppCode($vtsgn); 
            }
            $hash = "/recolte/certification/genre/appellation_ALSACEBLANC/mention/lieu/couleur/cepage_".$cepage_code;
            $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get($hash);
            return $config->getDouane()->getFullAppCode($vtsgn).$config->getDouane()->getCodeCepage();    
            
         case 'PINOTNOIR':
         case 'PINOTNOIRROUGE':
           $hash = str_replace('/declaration','/recolte',$cepage->getLieu()->getHash());
           $config = $cepage->getCouchdbDocument()->getConfigurationCampagne()->get($hash);
           return $config->getDouane()->getFullAppCode($vtsgn);     
         default:
             return $cepage->getConfig()->getDouane()->getFullAppCode($vtsgn).$cepage->getConfig()->getDouane()->getCodeCepage();     
     }
    }

}
