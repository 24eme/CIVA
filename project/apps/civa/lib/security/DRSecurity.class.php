<?php

class DRSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const CONSULTATION = 'CONSULTATION';
    const EDITION = 'EDITION';

    protected $instance;
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

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        if(!$this->dr) {

            return false;
        }

        return true;
    }

}