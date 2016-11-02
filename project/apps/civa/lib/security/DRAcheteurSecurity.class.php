<?php

class DRAcheteurSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

    protected $compte;
    protected $etablissement;

    public static function getInstance($compte) {

        return new DRAcheteurSecurity($compte);
    }

    public function __construct($compte) {
        $this->compte = $compte;
        $this->etablissement = DRClient::getInstance()->getEtablissementAcheteur($this->compte->getSociete());
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->etablissement) {

             return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        return true;
    }

}
