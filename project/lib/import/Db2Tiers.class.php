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
    const COL_TELEPHONE_PRO         = 37;
    const COL_TELEPHONE_PRIVE       = 38;
    const COL_FAX                   = 39;
    const COL_EMAIL                 = 40;
    const COL_SEXE_CHEF_ENTR        = 41;
    const COL_NOM_PRENOM_CHEF_ENTR  = 42;
    const COL_ADRESSE_SIEGE         = 46;
    const COL_CCV_REC               = 55;
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
    
    function isRecoltant() {
        return ($this->get(self::COL_CVI) && 
                    (($this->get(self::COL_RECOLTANT) == "O") || 
                    (($this->get(self::COL_RECOLTANT) == "N" || !$this->get(self::COL_RECOLTANT)) && !$this->get(self::COL_CIVABA))));
    }
    
    function isMetteurEnMarche() {
        return ($this->get(self::COL_CIVABA) && 
                ($this->get(self::COL_RECOLTANT) == "N" || !$this->get(self::COL_RECOLTANT)));
                   
    }

    function isAcheteur() {

        return $this->isMetteurEnMarche() && $this->get(self::COL_CVI);
    }

    function isCourtier() {

        return (!$this->isMetteurEnMarche() && !$this->isRecoltant() && !$this->isAcheteur()) && ($this->get(self::COL_NUM) > 90000 || $this->get(self::COL_COURTIER)) ;
    }
    
}
