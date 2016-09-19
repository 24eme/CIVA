<?php

class GammaSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

    protected $compte;

    public static function getInstance($compte) {

        return new GammaSecurity($compte);
    }

    public function __construct($compte) {
        $this->compte = $compte;
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        $hasNoAccises = false;
        foreach($this->compte->getSociete()->getEtablissementsObject() as $etablissement) {
            if($etablissement->no_accises) {
                $hasNoAccises = true;
            }
        }

        if(!$hasNoAccises) {

            return false;
        }

        return true;
    }

}
