<?php

class CompteSecurity implements SecurityInterface {

    protected $compte;

    public static function getInstance($compte) {

        return new EtablissementSecurity($compte);
    }

    public function __construct($compte) {
        $this->compte = $compte;
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
            $this->compte->id_societe != $this->getUser()->getCompte()->id_societe) {

            return false;
        }

        foreach($droits as $droit) {
            if(!$this->compte->hasDroit($droit)) {

                return false;
            }
        }

        return true;
    }

}
