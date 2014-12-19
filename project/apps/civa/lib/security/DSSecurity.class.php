<?php

class DSSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const CONSULTATION = 'CONSULTATION';
    const CREATION = 'CREATION';
    const EDITION = 'EDITION';

    protected $ds;
    protected $myUser;
    protected $type_ds;
    protected $tiers;

    public static function getInstance($myUser, $ds_principale = null, $type_ds = null) {

        return new DSSecurity($myUser, $ds_principale, $type_ds);
    }

    public function __construct($myUser, $ds_principale = null, $type_ds = null) {
        $this->myUser = $myUser;
        $this->ds = $ds_principale;
        if($this->ds) {
            $type_ds = $ds_principale->type_ds;
        }
        if(!$type_ds) {

            throw new sfException("Type DS unknow");
        }
        $this->tiers = $this->myUser->getDeclarantDS($type_ds);
        $this->type_ds = $type_ds;
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(!$this->tiers) {

            return false;
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
        
        if(!$this->tiers->hasLieuxStockage() && !$this->tiers->isAjoutLieuxDeStockage()) {

            return false;
        }

        if($this->tiers && $this->ds && $this->tiers->getIdentifiant() != $this->ds->identifiant) {

            throw new sfException("Pas sa DS");

            return false;
        }

        /*** CONSULTATION ***/

        if(in_array(self::CONSULTATION, $droits)) {

            return true;
        }

        /*** CREATION ***/

        if(in_array(self::CREATION, $droits) && $this->ds && !$this->ds->isNew()) {

            return false;
        }

        if(in_array(self::CREATION, $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::CREATION, $droits) && !$this->myUser->isDSEditable()) {

            return false;
        }

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

        if(in_array(self::EDITION , $droits) &&$this->myUser->hasCredential(myUser::CREDENTIAL_ADMIN)) {
                
            return true;
        }

        if(in_array(self::EDITION , $droits) && $this->ds->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION , $droits) && $this->myUser->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::EDITION , $droits) && !$this->myUser->isDSEditable()) {

            return false;
        }

        return true;
    }

}