<?php

class ExportDSCiva {
    
    protected $campagne;
    protected $ds_ids;
    protected $client_ds;
    protected $ds_liste;


    const CSV_DS_TRAITEE = 22; // "O" ou "N"
    const CSV_DS_DATE_SAISIE = 23; // JJMMAAAA
    
    
    public function __construct($campagne) {
        if(!preg_match('/^[0-9]{4}$/', $campagne)){
            throw new sfException("La campagne doit être une année ($campagne)");
        }
        $this->campagne = $campagne;
        $this->client_ds = acCouchdbManager::getClient("DSCiva");
        $this->ds_ids = $this->client_ds->getAllIdsByCampagne($this->campagne);
        $this->ds_liste = array();
        foreach ($this->ds_ids as $ds_id) {
            $this->ds_liste[] = $this->client_ds->find($ds_id);
        }
    }
    
    public function exportEntete() {
        $entete_string = ""; 
        foreach ($this->ds_liste as $cpt => $ds) {
            $entete_string.= $this->makeEnteteLigne($ds);
            if($cpt<(count($this->ds_liste)-1))
                $entete_string.= "\n";
        }
        return $entete_string;
    }
    
    public function exportLigne(){
        $ligne_string = ""; 
        foreach ($this->ds_liste as $cpt => $ds) {
            $ligne_string.= $this->makeLignes($ds);
            if($cpt<(count($this->ds_liste)-1))
                $ligne_string.= "\n";
            break;
        }
        return $ligne_string;
    }
    
    protected function makeEnteteLigne($ds) {
        $id_csv = substr($this->campagne, 2).$ds->numero_archive;
        $neant = ($ds->isDsPrincipale() && $ds->isDsNeant())? "\"N\"" : "\"P\"";
        $etb = $ds->getEtablissement();
        $lieu_stockage = "";//($ds->declarant->exist("adresse"))? $ds->declarant->adresse : ""; 
        $principale = ($ds->isDsPrincipale())? "\"P\"" : "\"S\"";
        $num_db2 = ($etb->exist('db2') && $etb->db2->exist('num'))? $etb->db2->num : '';
        $cvi = "\"".$ds->identifiant."\"";
        $civagene0 = "0"; // A trouvé
        
        
        $row = $this->campagne.",".$id_csv.",".$neant.",\"".$lieu_stockage."\",".$principale.",".$num_db2.",".$cvi.",".$civagene0.",";
        
        //VINTABLE
        $vin_table_volume = 0;
        if($ds->exist('declaration') && $ds->declaration->hasVinTable()){
            $vin_table = $ds->declaration->getVinTable();
            if(($vin_table->total_vt && $vin_table->total_vt > 0) ||
               ($vin_table->total_sgn && $vin_table->total_sgn > 0)){
               throw new sfException("L'appellation vin de table contient du vt ou du sgn pour : ".$ds->_id." ?");
            }
            $vin_table_volume = $vin_table->total_normal;
        }
       
        // Alsace Blanc + PinotNOIR + PinotNOIRROUGE 
        $stock_aoc_normal = 0;
        $stock_aoc_vt = 0;
        $stock_aoc_sgn = 0;
        if($ds->exist('declaration') && $ds->declaration->hasAlsaceBlanc()){
            $alsaceBlanc = $ds->declaration->getAlsaceBlanc();
            $stock_aoc_normal+= ($alsaceBlanc->total_normal)? $alsaceBlanc->total_normal : 0;
            $stock_aoc_vt += ($alsaceBlanc->total_vt)? $alsaceBlanc->total_vt : 0;
            $stock_aoc_sgn += ($alsaceBlanc->total_sgn)? $alsaceBlanc->total_sgn : 0;
        }
        
        if($ds->exist('declaration') && $ds->declaration->hasCommunale()){
            $communale = $ds->declaration->getCommunale();
            $stock_aoc_normal+= ($communale->total_normal)? $communale->total_normal : 0;
            $stock_aoc_vt += ($communale->total_vt)? $communale->total_vt : 0;
            $stock_aoc_sgn += ($communale->total_sgn)? $communale->total_sgn : 0;
        }
        
         if($ds->exist('declaration') && $ds->declaration->hasLieuDit()){
            $lieuDits = $ds->declaration->getLieuDit();
            $stock_aoc_normal+= ($lieuDits->total_normal)? $lieuDits->total_normal : 0;
            $stock_aoc_vt += ($lieuDits->total_vt)? $lieuDits->total_vt : 0;
            $stock_aoc_sgn += ($lieuDits->total_sgn)? $lieuDits->total_sgn : 0;
        }
        
        if($ds->exist('declaration') && $ds->declaration->hasPinotNoir()){
            $pinotNoir = $ds->declaration->getPinotNoir();
            $stock_aoc_normal+= ($pinotNoir->total_normal)? $pinotNoir->total_normal : 0;
            $stock_aoc_vt += ($pinotNoir->total_vt)? $pinotNoir->total_vt : 0;
            $stock_aoc_sgn += ($pinotNoir->total_sgn)? $pinotNoir->total_sgn : 0;
        }
        
        if($ds->exist('declaration') && $ds->declaration->hasPinotNoirRouge()){
            $pinotNoirRouge = $ds->declaration->getPinotNoirRouge();
            $stock_aoc_normal+= ($pinotNoirRouge->total_normal)? $pinotNoirRouge->total_normal : 0;
            $stock_aoc_vt += ($pinotNoirRouge->total_vt)? $pinotNoirRouge->total_vt : 0;
            $stock_aoc_sgn += ($pinotNoirRouge->total_sgn)? $pinotNoirRouge->total_sgn : 0;
        }
        
        $row .= $this->convertToFloat($stock_aoc_normal).",".$this->convertToFloat($stock_aoc_vt).",".$this->convertToFloat($stock_aoc_sgn).",";
        
        
        //GRAND CRU
        if($ds->exist('declaration') && $ds->declaration->hasGrdCru()){
            $grdCrus = $ds->declaration->getGrdCru();
            $row .= $this->convertToFloat($grdCrus->total_normal).",".$this->convertToFloat($grdCrus->total_vt).",".$this->convertToFloat($grdCrus->total_sgn).",";
        }else{
            $row .= ".00,.00,.00,"; 
        }
        
        //CREMANT
        if($ds->exist('declaration') && $ds->declaration->hasCremant()){
            $cremant = $ds->declaration->getCremant();
            if(($cremant->total_vt && $cremant->total_vt > 0) ||
               ($cremant->total_sgn && $cremant->total_sgn > 0)){
               throw new sfException("L'appellation crémant contient du vt ou du sgn pour : ".$ds->_id." ?");
            }
            $row .= $this->convertToFloat($cremant->total_normal).",";
        }else{
            $row .= ".00,";
        }
        
        if($ds->exist('declaration') && $ds->declaration->exist('certification')){
            $certif = $ds->declaration->certification;        
            $row .= $this->convertToFloat($certif->total_normal - $vin_table_volume).",".$this->convertToFloat($certif->total_vt).",".$this->convertToFloat($certif->total_sgn).",";
        }else{
            $row .= ".00,.00,.00,"; 
        }
        
        //VINTABLE
        if($vin_table_volume > 0){
            $row .= $this->convertToFloat($vin_table_volume).",";
        }else{
            $row .= ".00,"; 
        }
        
        $date = $this->getDateForExport($ds);
       
        $row .= $this->convertToFloat($ds->mouts).","; 
        $row .= $this->convertToFloat($ds->dplc).","; 
        $row .= $this->convertToFloat($ds->rebeches).","; 
        $row .= ($ds->isValidee())? "\"O\"" : "\"N\"";
        $row .= ",".$date;
        
        return $row;
    }
    
    protected function makeLignes($ds) {
        $row = "";
        $id_csv = substr($this->campagne, 2).$ds->numero_archive;
        $row.= $id_csv;
        return $row;
    }


    protected function convertToFloat($vol) {
       if(!$vol) return ".00"; 
       $result = sprintf("%01.02f", round(str_replace(",", ".", $vol) * 1, 2));
       if($vol<1) return substr($result,1);
       return $result;
    }
    
    protected function getDateForExport($ds) {
        if($ds->exist('modifiee')){
            $dateArr = array();
            if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $ds->modifiee, $dateArr)){
                $a = $dateArr[1];
                $m = $dateArr[2];
                $j = $dateArr[3];
            }
            return ($j*1).$m.$a;
        }
        if($ds->exist('validee')){
            $dateArr = array();
            if(preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $ds->validee, $dateArr)){
                $a = $dateArr[1];
                $m = $dateArr[2];
                $j = $dateArr[3];
            }
            return ($j*1).$m.$a;
        }
        return '';
    }
}
