<?php

class DRSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const VISUALISATION = 'VISUALISATION';
    const EDITION = 'EDITION';
    const DEVALIDATION = 'DEVALIDATION_TIERS';
    const DEVALIDATION_CIVA = 'DEVALIDATION_CIVA';

    protected $dr;
    protected $sfUser;
    protected $etablissement;

    public static function getInstance($compte, $dr = null) {

        return new DRSecurity($compte, $dr);
    }

    public function __construct($etablissement, $dr = null) {
        $this->dr = $dr;
        $this->sfUser = sfContext::getInstance()->getUser();
        $this->etablissement = $etablissement;
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

        /*if(!$this->compte->hasDroit(_CompteClient::DROIT_DR_RECOLTANT)) {

            return false;
        }*/

        if(in_array(self::DECLARANT, $droits) && !$this->sfUser->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN) &&
          ($this->etablissement->identifiant != $this->sfUser->getCompte()->identifiant)){
            return false;
        }


        if($this->etablissement && $this->dr &&
            (($this->etablissement->getIdentifiant() != $this->dr->getEtablissement()->getIdentifiant())
              || ($this->sfUser->getCompte()->identifiant
              != $this->dr->getEtablissement()->getIdentifiant()))) {
            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)
                                            && $this->dr
                                            && $this->dr->isValideeCiva()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {

            return true;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR)
                                            && $this->dr
                                            && $this->dr->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->sfUser->hasCredential(CompteSecurityUser::CREDENTIAL_OPERATEUR)) {

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
