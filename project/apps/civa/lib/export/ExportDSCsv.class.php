<?php 

class ExportDSCsv {
    protected $dss = null;
    protected $ds_principale = null;

    public function __construct($identifiant, $periode) {
        $this->dss = DSCivaClient::getInstance()->findDssByCviAndPeriode($identifiant, $periode, acCouchdbClient::HYDRATE_JSON);
        foreach($this->dss as $ds) {
            if(isset($ds->ds_principale) && $ds->ds_principale === 0) {
                continue;
            }
            $this->ds_principale = $ds;
        }
    }

    public function output() {
        $output = null;
        foreach($this->dss as $ds) {
            $output .= $this->outputDS($ds);
        }

        return $output;
    }

    protected function outputDS($ds) {
        $output = null;
        $validee = (isset($this->ds_principale->validee) && $this->ds_principale->validee) ? $this->ds_principale->validee : null; 
        $modifiee = (isset($this->ds_principale->modifiee) && $this->ds_principale->modifiee) ? $this->ds_principale->modifiee : null;
        $date_depot_mairie = isset($this->ds_principale->date_depot_mairie) ? $this->ds_principale->date_depot_mairie : null;
        $principale = ($this->ds_principale->_id == $ds->_id) ? "PRINCIPALE" : "SECONDAIRE";
        $statut = ($modifiee) ? "VALIDE" : (($validee) ? "NON_VALIDE_CIVA" : "EN_COURS");
        
        $ligneStart = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $ds->periode, $ds->_id, $ds->declarant->nom, $ds->identifiant, $ds->stockage->numero, $principale, $statut, $validee, $modifiee, $date_depot_mairie);

        if(isset($ds->declaration->certification->genre)) {
            foreach($ds->declaration->certification->genre as $appellation_key => $appellation) {
                if(!preg_match("/appellation_/", $appellation_key)) {
                    
                    continue;
                }

                foreach($appellation->mention as $lieu_key => $lieu) {
                    if(!preg_match("/lieu/", $lieu_key)) {
                        
                        continue;
                    }

                    foreach($lieu as $couleur_key => $couleur) {
                        if(!preg_match("/couleur/", $couleur_key)) {
                        
                            continue;
                        }

                        foreach($couleur as $cepage_key => $cepage) {
                            if(!preg_match("/cepage/", $cepage_key)) {
                        
                                continue;
                            }

                            $hasLieuDit = false;
                            foreach($cepage->detail as $detail) {
                                $total = $detail->volume_normal + $detail->volume_vt + $detail->volume_sgn;
                                $hasLieuDit = !$lieu->libelle && $detail->lieu;
                                if($hasLieuDit) {
                                    $output .= sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $lieu->libelle, $couleur->libelle,$cepage->libelle,$detail->lieu, $total, ($detail->volume_normal) ? $detail->volume_normal : 0, ($detail->volume_vt) ? $detail->volume_vt : 0, ($detail->volume_sgn) ? $detail->volume_sgn : 0);
                                }
                            }

                            $output .= sprintf("%s;%s;%s;%s;%s;TOTAL;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $lieu->libelle, $couleur->libelle, $cepage->libelle, $couleur->total_stock, $couleur->total_normal, $couleur->total_vt, $couleur->total_sgn);
                        }

                        if($couleur_key == "couleur") {
                            
                            continue;
                        }
                        
                        $output .= sprintf("%s;%s;%s;%s;TOTAL;;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $lieu->libelle, $couleur->libelle, $couleur->total_stock, $couleur->total_normal, $couleur->total_vt, $couleur->total_sgn);
                    }

                    if($lieu_key == "lieu") {
                        
                        continue;
                    }

                    $output .= sprintf("%s;%s;%s;TOTAL;;;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $lieu->libelle, $lieu->total_stock, $lieu->total_normal, $lieu->total_vt, $lieu->total_sgn);
                }

                $output .= sprintf("%s;%s;TOTAL;;;;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $appellation->total_stock, $appellation->total_normal, $appellation->total_vt, $appellation->total_sgn);
            }
        }

        if($ds->mouts) {
            $output .= sprintf("%s;mouts;;;;;%s;;;\n", $ligneStart, $ds->mouts);
        }
        if($ds->rebeches) {
            $output .= sprintf("%s;rebeches;;;;;%s;;;\n", $ligneStart, $ds->rebeches);
        }
        if($ds->dplc) {
            $output .= sprintf("%s;dplc;;;;;%s;;;\n", $ligneStart, $ds->dplc);
        }
        if($ds->lies) {
            $output .= sprintf("%s;lies;;;;;%s;;;\n", $ligneStart, $ds->lies);
        }

        if(!$output) {
            $output .= sprintf("%s;;;;;;;;;\n", $ligneStart);
        }

        return $output;
    }

}