<?php

class EtablissementSecurity implements SecurityInterface {

    protected $etablissement;

    public static function getInstance($etablissement) {

        return new EtablissementSecurity($etablissement);
    }

    public function __construct($etablissement) {
        $this->etablissement = $etablissement;
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }

    public function isAuthorized($droits = array()) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(!$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN) &&
           !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR) &&
            $this->etablissement->id_societe != $this->getUser()->getCompte()->id_societe) {

            return false;
        }

        foreach($droits as $droit) {
            if(!$this->etablissement->hasDroit($droit)) {

                return false;
            }
        }

        return true;
    }

}
