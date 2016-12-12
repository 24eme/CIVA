<?php

class DRSecurity implements SecurityInterface {

    const CONSULTATION = 'CONSULTATION';
    const EDITION = 'EDITION';
    const ADMIN = 'ADMIN';

    protected $dr;
    protected $etablissement;

    public static function getInstance($dr, $etablissement = null) {

        return new DRSecurity($dr, $etablissement);
    }

    public function __construct($dr, $etablissement = null) {
        $this->dr = $dr;
        if($this->dr) {
            $this->etablissement = $this->dr->getEtablissement();
        }
        if($etablissement) {
            $this->etablissement = $etablissement;
        }
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(!$this->etablissement) {

            return false;
        }

        if(!EtablissementSecurity::getInstance($this->etablissement)->isAuthorized(Roles::TELEDECLARATION_DR)) {

            return false;
        }

        if(in_array(self::CONSULTATION, $droits)) {

            return true;
        }

        if(in_array(self::EDITION, $droits) && !DRClient::getInstance()->isTeledeclarationOuverte() && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN) && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->dr && $this->dr->isValideeCiva()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->dr && $this->dr->isValideeTiers() && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {

            return false;
        }

        if(in_array(self::EDITION, $droits)) {

            return true;
        }

        return true;
    }

}
