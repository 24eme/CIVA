<?php

class GammaSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

    protected $compte;
    protected $etablissement;

    public static function getInstance($etablissement) {

        return new GammaSecurity($etablissement);
    }

    public function __construct($etablissement) {
        $this->etablissement = $etablissement;
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->etablissement && !$this->etablissement->no_accises) {

            return false;
        }

        return true;
    }

}
