<?php

class DRSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const EDITION = 'EDITION';
    const DEVALIDATION = 'DEVALIDATION_TIERS';
    const DEVALIDATION_CIVA = 'DEVALIDATION_CIVA';

    protected $dr;
    protected $myUser;
    protected $tiers;

    public static function getInstance($myUser, $dr = null) {

        return new DRSecurity($myUser, $dr);
    }

    public function __construct($myUser, $dr = null) {
        $this->myUser = $myUser;
        $this->dr = $dr;
        $this->tiers = $this->myUser->getDeclarant();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->tiers->isDeclarantDR()) {

            return false;
        }

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_DR_RECOLTANT)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_ADMIN)
                                            && $this->dr
                                            && $this->dr->isValideeCiva()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_ADMIN)) {

            return true;
        }

        if(in_array(self::EDITION, $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_OPERATEUR)
                                            && $this->dr
                                            && $this->dr->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

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