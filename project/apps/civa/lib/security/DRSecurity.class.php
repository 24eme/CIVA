<?php

class DRSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const EDITION = 'EDITION';
    const DEVALIDATION = 'DEVALIDATION_TIERS';
    const DEVALIDATION_CIVA = 'DEVALIDATION_CIVA';

    protected $dr;
    protected $compte;
    protected $sfUser;
    protected $etablissement;

    public static function getInstance($compte, $dr = null) {

        return new DRSecurity($compte, $dr);
    }

    public function __construct($compte, $dr = null) {
        $this->compte = $compte;
        $this->dr = $dr;
        $this->sfUser = sfContext::getInstance()->getUser();
        $this->etablissement = DRClient::getInstance()->getEtablissement($this->compte->getSociete());
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/
        if(!$this->etablissement) {

            return false;
        }

        if(!in_array($this->etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))) {

            return false;
        }

        if(!$this->compte->hasDroit(_CompteClient::DROIT_DR_RECOLTANT)) {

            //return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential($compte::CREDENTIAL_ADMIN)
                                            && $this->dr
                                            && $this->dr->isValideeCiva()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential($compte::CREDENTIAL_ADMIN)) {

            return true;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential($compte::CREDENTIAL_OPERATEUR)
                                            && $this->dr
                                            && $this->dr->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential($compte::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::EDITION, $droits) && !CurrentClient::getCurrent()->isDREditable()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->dr && $this->dr->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION, $droits)) {

            return true;
        }

        return true;
    }

}
