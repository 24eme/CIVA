<?php

class DRAcheteurSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

    protected $myUser;
    protected $tiers;

    public static function getInstance($myUser) {

        return new DRAcheteurSecurity($myUser);
    }

    public function __construct($myUser) {
        $this->myUser = $myUser;
        $this->tiers = $this->myUser->getDeclarant();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->tiers->isDeclarantDRAcheteur()) {

            return false;
        }

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_DR_ACHETEUR)) {

            return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        return true;
    }

}