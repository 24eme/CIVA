<?php
/**
 * Model for Annuaire
 *
 */

class Annuaire extends BaseAnnuaire 
{
    public function constructId() 
    {
        $this->set('_id', AnnuaireClient::getInstance()->buildId($this->cvi));
    }

    public function getAnnuaireSorted($type) {
        $annuaires = $this->get($type)->toArray(true, false);

        uasort($annuaires, "Annuaire::sortByNom");

        return $annuaires;
    }

    public static function sortByNom($a, $b) {
        $regexp_intitules = "/^(CAVES|DOMAINE|EARL|EAR|EURL|GAEC|GFA, DU|HERITIERS|INDIVISION||MADAME|MADEME|MAISON|MELLE|M\., ET, MME|MLLE|MM\.|MME, VEUVE|MMES|MME|MRS|S\.A\.|SARL|S\.A\.S\.|SASU|SAS|S\.C\.A\.|SCA|SCEA|S\.C\.I\.|SCI|S\.D\.F\.|SDF|SICA|STEF|STE|VEUVE|VINS|SA|M\.|S\.) /";

        $a_formated = preg_replace($regexp_intitules, "", $a);
        $b_formated = preg_replace($regexp_intitules, "", $b);

        return $a_formated > $b_formated;
    }

}