<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Db2Tiers
 *
 * @author vince
 */
class Db2Tiers extends Db2 {
    const COL_NUM                   = 0;
    const COL_CIVABA                = 1;
    const COL_NO_STOCK              = 3;
    const COL_HAS_DRM               = 4;
    const COL_NOM_PRENOM            = 6;
    const COL_ANNEE_NAISSANCE       = 8;
    const COL_INTITULE              = 9;
    const COL_MAISON_MERE           = 10;
    const COL_TYPE_DECLARATION      = 11;
    const COL_NUMERO                = 12;
    const COL_ADRESSE               = 13;
    const COL_COMMUNE               = 14;
    const COL_CODE_POSTAL           = 15;
    const COL_TYPE_TIERS            = 17;
    const COL_RECOLTANT             = 23;
    const COL_COURTIER              = 31;
    const COL_CLOTURE               = 35;
    const COL_TELEPHONE_PRO         = 37;
    const COL_TELEPHONE_PRIVE       = 38;
    const COL_FAX                   = 39;
    const COL_EMAIL                 = 40;
    const COL_SEXE_CHEF_ENTR        = 41;
    const COL_NOM_PRENOM_CHEF_ENTR  = 42;
    const COL_DATE_CREATION         = 43;
    const COL_DATE_CLOTURE          = 44;
    const COL_ADRESSE_SIEGE         = 46;
    const COL_CCV_REC               = 55;
    const COL_DS_DECEMBRE           = 56;
    const COL_CVI                   = 57;
    const COL_SIRET                 = 58;
    const COL_INSEE_SIEGE           = 59;
    const COL_CODE_POSTAL_SIEGE     = 60;
    const COL_COMMUNE_SIEGE         = 61;
    const COL_INSEE_DECLARATION     = 62;
    const COL_JOUR_NAISSANCE        = 68;
    const COL_MOIS_NAISSANCE        = 69;
    const COL_NO_ASSICES            = 70;
    const COL_SITE_INTERNET         = 82;

    public function isRecoltant() {
        return ($this->get(self::COL_CVI) &&
                    (($this->get(self::COL_RECOLTANT) == "O") ||
                    (($this->get(self::COL_RECOLTANT) == "N" || !$this->get(self::COL_RECOLTANT)) && !$this->get(self::COL_CIVABA))));
    }

    public function isMetteurEnMarche() {
        return ($this->get(self::COL_CIVABA) &&
                ($this->get(self::COL_RECOLTANT) == "N" || !$this->get(self::COL_RECOLTANT)));

    }

    public function isCloture() {

        return $this->get(self::COL_CLOTURE) == "O";
    }

    public function isAcheteur() {

        return $this->isMetteurEnMarche() && $this->get(self::COL_CVI);
    }

    public function isCourtier() {

        return (!$this->isMetteurEnMarche() && !$this->isRecoltant() && !$this->isAcheteur()) && ($this->get(self::COL_NUM) > 90000 || $this->get(self::COL_COURTIER)) ;
    }

    public function isEtablissement() {

        return ($this->isRecoltant() || $this->isMetteurEnMarche() || $this->isCourtier());
    }

    public function getFamille() {
        if($this->isRecoltant()) {

          return EtablissementFamilles::FAMILLE_PRODUCTEUR;
        }

        if($this->isCourtier()) {

          return EtablissementFamilles::FAMILLE_COURTIER;
        }

        if($this->get(Db2Tiers::COL_TYPE_TIERS) == 'PN' || $this->get(Db2Tiers::COL_TYPE_TIERS) == 'SIC') {

          return EtablissementFamilles::FAMILLE_NEGOCIANT;
        }

        if($this->get(Db2Tiers::COL_TYPE_TIERS) == 'CCV') {

          return EtablissementFamilles::FAMILLE_COOPERATIVE;
        }

        if(preg_match("/^V/", $this->get(Db2Tiers::COL_TYPE_TIERS))) {

          return EtablissementFamilles::FAMILLE_PRODUCTEUR;
        }

        if($this->isMetteurEnMarche()) {

            return EtablissementFamilles::FAMILLE_NEGOCIANT;
        }

        return null;
    }

    public function isProducteurVinificateur() {

        return ($this->getFamille() == EtablissementFamilles::FAMILLE_PRODUCTEUR && preg_match("/^(VRT|VRP|VVV)$/", $this->get(Db2Tiers::COL_TYPE_TIERS)));
    }

    public function printDebug() {
        echo "  TIERS \n";
        echo "   NUM   : ".$this->get(Db2Tiers::COL_NUM)."\n";
        echo "   NSTOCK: ".$this->get(Db2Tiers::COL_NO_STOCK)."\n";
        echo "   NMERE : ".$this->get(Db2Tiers::COL_MAISON_MERE)."\n";
        echo "   NOM   : ".$this->get(Db2Tiers::COL_NOM_PRENOM)."\n";
        echo "   CVI   : ".$this->get(Db2Tiers::COL_CVI)."\n";
        echo "   CIVABA: ".$this->get(Db2Tiers::COL_CIVABA)."\n";
        echo "   ACCISE: ".$this->get(Db2Tiers::COL_NO_ASSICES)."\n";
        echo "   TYPE  : ".$this->get(Db2Tiers::COL_TYPE_TIERS)."\n";
        echo "   SUSPENDU  : ".$this->isCloture()."\n";
        echo "   FAMILLE  : ".$this->getFamille()."\n";
    }

}
