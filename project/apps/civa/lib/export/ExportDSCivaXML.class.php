<?php

class ExportDSCivaXML {

    protected $periode;
    protected $campagne;
    protected $ds_liste;
    protected $stats;

    public function __construct($periode, $types_ds = array(DSCivaClient::TYPE_DS_PROPRIETE, DSCivaClient::TYPE_DS_NEGOCE)) {
        if (!preg_match('/^[0-9]{6}$/', $periode)) {
            throw new sfException("La période doit être au format yyyymm ($periode)");
        }
        $this->periode = $periode;
        $this->campagne = substr($periode, 0, 4);
        $this->ds_liste = array();
        foreach (DSCivaClient::getInstance()->getAllIdsByPeriode($this->periode, $types_ds) as $ds_id) {
            $ds = DSCivaClient::getInstance()->find($ds_id);
            if (!$ds) {
              continue;
            }
            $ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);
            if (preg_match('/^C?(67|68)/', $ds->identifiant) && $ds_principale->isValidee()) {
                $this->ds_liste[$ds_principale->_id] = $ds_principale;
                $this->ds_liste[$ds->_id] = $ds;
            }
        }
    }

    public function exportXml() {
        $export_xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>" . "\r\n\r\n";
        $export_xml.="<listeDecStock>\r\n";
        $lieux_stockage_num = array();
        $current_cvi = null;
        $this->ds_liste["9999999999999"] = null;
        foreach ($this->ds_liste as $ds) {
            if((!$ds || $ds->declarant->cvi != $current_cvi) && !is_null($current_cvi)) {
                foreach($lieux_stockage_num as $num) {
                    $export_xml.= $this->getXMLDSAutre($num);
                }
                $export_xml.="\t</decStock>\r\n";
            }
            if(!$ds) {
                continue;
            }
            if($ds->declarant->cvi != $current_cvi) {
                $lieux_stockage_num = array();
                foreach($ds->getEtablissementObject()->lieux_stockage as $num => $lieu) {
                    if(strpos($num, $ds->declarant->cvi) === false) {
                        continue;
                    } 
                    $lieux_stockage_num[$num] = $num;
                }
                $current_cvi = $ds->declarant->cvi;
                $export_xml.="\t<decStock numCvi=\"" . $current_cvi . "\" dateDepot=\"".$ds->validee."\">\r\n";
            }
            $export_xml.= $this->makeXMLDS($ds);
            unset($lieux_stockage_num[$ds->stockage->numero]);
        }
        $export_xml.="</listeDecStock>\r\n";
        return $export_xml;
    }

    protected function getXMLDSAutre($lieu_stockage, $ds = null) {
        $xml = "";

        $xml.= $this->makeXMLDSLigne($lieu_stockage, "LIES", $this->convertToFloat(($ds) ? $ds->lies : 0, false), "Lies");
        $xml.= $this->makeXMLDSLigne($lieu_stockage, "VDRA", $this->convertToFloat(($ds) ? $ds->dplc : 0, false), "DPLC");
        $xml.= $this->makeXMLDSLigne($lieu_stockage, "REBECHE", $this->convertToFloat(($ds) ? $ds->rebeches : 0, false), "Rebêches");
        $xml.= $this->makeXMLDSLigne($lieu_stockage, "VNC", $this->convertToFloat(0, false), "Vins non conformes");
        
        return $xml;
    }

    protected function makeXMLDS($ds) {
        $xml = "";
        $lieu_stockage = $ds->identifiant . $ds->getLieuStockage();

        $xml.= $this->getXMLDSAutre($lieu_stockage, $ds);
        
        $lignes = array();
        foreach($ds->getProduits() as $produit) {
            if($produit->volume_normal) {
                @$lignes[$produit->getConfig()->getCodeDouane() . ";" . $produit->getConfig()->getLibelleFormat()] += $produit->volume_normal;
            }
            if($produit->volume_vt) {
                @$lignes[$produit->getConfig()->getCodeDouane("VT") . ";" . $produit->getConfig()->getLibelleFormat()." VT"] += $produit->volume_vt;
            }
            if($produit->volume_sgn) {
                @$lignes[$produit->getConfig()->getCodeDouane("SGN") . ";" . $produit->getConfig()->getLibelleFormat()." SGN"] += $produit->volume_sgn;
            }
        }

        foreach ($lignes as $key => $volume) {
            $codeDouane = preg_replace("/;.*$/", "", $key);
            $libelleProduit = preg_replace("/^.*;/", "", $key);
            $xml.= $this->makeXMLDSLigne($lieu_stockage, preg_replace("/,.*/", "", $codeDouane), $volume, $libelleProduit);
        }
        
        if ($ds->hasMouts()) {
            $xml .= $this->makeXMLDSLigne($lieu_stockage, "MCR", $ds->getMouts(), "Moûts");
        }

        return $xml;
    }
    
    protected function makeXMLDSLigne($lieu_stockage, $codeDouane, $volume, $libelleProduit = null) {
        if($codeDouane == 'VT_SANS_IG_AUTRES' && strpos($libelleProduit, 'Blanc') !== null) {
            $codeDouane = '4B999';
        }
        if($codeDouane == 'VT_SANS_IG_AUTRES' && strpos($libelleProduit, 'Rosé') !== null) {
            $codeDouane = '4S999';
        }
        if($codeDouane == 'VT_SANS_IG_AUTRES' && strpos($libelleProduit, 'Rouge') !== null) {
            $codeDouane = '4R999';
        }

        $codeDouane = str_replace("1R001S 1", "1R001S", $codeDouane);
        $codeDouane = str_replace("1S001S 1", "1S001S", $codeDouane);

        $ligne = "\t\t<ligne>\r\n";
        $ligne .= "\t\t\t<codeInstallation>" . $lieu_stockage . "</codeInstallation>\r\n";
        $ligne .= "\t\t\t<codeProduit>" . $codeDouane . "</codeProduit>\r\n";
        $ligne .= "\t\t\t<volume>" . $this->convertToFloat($volume, false) . "</volume>\r\n";
        $ligne .= "\t\t</ligne>\r\n";
        
        @$this->stats[$libelleProduit.";".$codeDouane] += $volume;
        
        return $ligne;
    }
    
    public function getStats() {
        
        return $this->stats;
    }

    protected function convertToFloat($vol, $withTrunc = true) {
        if (!$vol)
            return ($withTrunc) ? ".00" : "0.00";
        $result = sprintf("%01.02f", round(str_replace(",", ".", $vol) * 1, 2));
        if ($withTrunc && $vol < 1)
            return substr($result, 1);
        return $result;
    }

}
