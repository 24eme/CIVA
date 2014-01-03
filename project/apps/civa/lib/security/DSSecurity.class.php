<?php

class DSSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const CONSULTATION = 'CONSULTATION';
    const CREATION = 'EDITION';
    const EDITION = 'EDITION';

    protected $instance;
    protected $ds;
    protected $myUser;
    protected $tiers;

    public static function getInstance($myUser, $ds_principale = null) {

        return new DSSecurity($myUser, $ds_principale = null);
    }

    public function __construct($myUser, $ds_principale = null) {
        $this->myUser = $myUser;
        $this->ds = $ds_principale;
        $this->tiers = $this->myUser->getDeclarant();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->tiers->isDeclarantStock()) {
            
            return false;
        }

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_DS_DECLARANT)) {
            
            return false;
        }
        
        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        /*** CREATION ***/

        if(in_array(self::CREATION, $droits)) {

            return true;
        }

        /*** EDITION ***/

        if(!$this->ds) {

            return false;
        }

        if(in_array(self::EDITION , $droits) && $this->ds->isValideeCiva()) {

            return false;
        }

        if($this->myUser->hasCredential(myUser::CREDENTIAL_ADMIN)) {
            
            return true;
        }

        if(in_array(self::EDITION , $droits) && $this->ds->isValideeTiers()) {

            return false;
        }

        if($this->myUser->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::EDITION , $droits) && !CurrentClient::getCurrent()->isDSEditable()) {

            return false;
        }

        if($this->myUser->getDeclarant()->getIdentifiant() != $this->ds->identifiant) {

            return false;
        }

        return true;
    }

}