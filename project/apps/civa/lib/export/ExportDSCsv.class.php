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
        $userEditeur = str_replace("COMPTE-", "", key($this->ds_principale->utilisateurs->edition));
        $userValideur = str_replace("COMPTE-", "", key($this->ds_principale->utilisateurs->validation));


        $ligneStart = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $ds->periode, $ds->_id, $ds->declarant->nom, $ds->identifiant, $ds->stockage->numero, $principale, $statut, $validee, $modifiee, $date_depot_mairie, $userEditeur, $userValideur);

        foreach($ds->declaration as $certification_key => $certification) {
            if(!preg_match("/certification/", $certification_key)) {

                continue;
            }
            foreach($certification as $genre_key => $genre) {
            if(!preg_match("/genre/", $genre_key)) {

                continue;
            }
            foreach($genre as $appellation_key => $appellation) {
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
                                    $output .= sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n", $ligneStart, trim($genre->libelle." ".$appellation->libelle), $lieu->libelle, $couleur->libelle,$cepage->libelle,($detail->lieu) ? "\"".$detail->lieu."\"" : null, $total, ($detail->volume_normal) ? $detail->volume_normal : 0, ($detail->volume_vt) ? $detail->volume_vt : 0, ($detail->volume_sgn) ? $detail->volume_sgn : 0);
                                }

                                //$output .= sprintf("%s;%s;%s;%s;%s;TOTAL;%s;%s;%s;%s\n", $ligneStart, $appellation->libelle, $lieu->libelle, $couleur->libelle, $cepage->libelle, $cepage->total_stock, $cepage->total_normal, $cepage->total_vt, $cepage->total_sgn);
                            }
                        }
                    }
                }
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
