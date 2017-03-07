<?php

class GammaSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

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

        if(!EtablissementSecurity::getInstance($this->etablissement)->isAuthorized(Roles::TELEDECLARATION_GAMMA)) {
            return false;
        }

        return true;
    }

}
