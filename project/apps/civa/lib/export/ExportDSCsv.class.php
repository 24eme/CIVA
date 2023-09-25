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

    public static function getHeader() {

        return "campagne;periode;type_ds;identifiant;nom;cvi;civaba;famille;numero_stokage;principale;appellation;lieu;cepage;denomination;volume_total;volume_normal;volume_vt;volume_sgn;statut;date_validation_tiers;date_validation_civa;date_depot_mairie;editeur;validateur;id_doc\n";
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

        $etablissement = EtablissementClient::getInstance()->find($ds->identifiant, acCouchdbClient::HYDRATE_JSON);
        if(!$etablissement) {
            $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-C".$ds->civaba, acCouchdbClient::HYDRATE_JSON);
        }
        $ligneStart = sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", substr($ds->periode, 0, 4), $ds->periode, $ds->type_ds, $etablissement->identifiant, $ds->declarant->nom, $ds->declarant->cvi, (isset($ds->civaba) ? $ds->civaba : null), $etablissement->famille, $ds->stockage->numero, $principale);
        $ligneEnd = sprintf("%s;%s;%s;%s;%s;%s;%s\n", $statut, $validee, $modifiee, $date_depot_mairie, $userEditeur, $userValideur, $ds->_id);

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
                                    if(!isset($genre->libelle)) {
                                        $genre->libelle = null;
                                    }
                                    $appellationLibelle = trim(trim($genre->libelle." ".$appellation->libelle) .' '.$couleur->libelle);
                                    if(preg_match("/alsace/i", $appellationLibelle) && !preg_match("/aoc/i", $appellationLibelle)) {
                                        $appellationLibelle = "AOC ".$appellationLibelle;
                                    }
                                    $output .= sprintf("%s;%s;%s;%s;%s;%s;%s;%s;%s;%s", $ligneStart, $appellationLibelle, $lieu->libelle, $cepage->libelle,($detail->lieu) ? "\"".$detail->lieu."\"" : null,  $total, ($detail->volume_normal) ? $detail->volume_normal : 0, ($detail->volume_vt) ? $detail->volume_vt : 0, ($detail->volume_sgn) ? $detail->volume_sgn : 0, $ligneEnd);
                                }
                            }
                        }
                    }
                }
            }
        }
        if($ds->mouts) {
            $output .= sprintf("%s;mouts;;;;%s;;;;%s", $ligneStart, $ds->mouts, $ligneEnd);
        }
        if($ds->rebeches) {
            $output .= sprintf("%s;rebeches;;;;%s;;;;%s", $ligneStart, $ds->rebeches, $ligneEnd);
        }
        if($ds->dplc) {
            $output .= sprintf("%s;dplc;;;;%s;;;;%s", $ligneStart, $ds->dplc, $ligneEnd);
        }
        if($ds->lies) {
            $output .= sprintf("%s;lies;;;;%s;;;;%s", $ligneStart, $ds->lies, $ligneEnd);
        }

        if(!$output) {
            $output .= sprintf("%s;;;;;;;;;%s", $ligneStart, $ligneEnd);
        }

        return $output;
    }

}
