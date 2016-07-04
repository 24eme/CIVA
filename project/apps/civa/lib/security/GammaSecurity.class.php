<?php

class GammaSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';

    protected $myUser;
    protected $tiers;

    public static function getInstance($myUser) {

        return new GammaSecurity($myUser);
    }

    public function __construct($myUser) {
        $this->myUser = $myUser;
        try {
            $this->tiers = $this->myUser->getTiers('MetteurEnMarche');
        } catch (Exception $e) {
            $this->tiers = new MetteurEnMarche();
        }
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!isset($this->tiers)) {

            return false;
        }

        if(!$this->tiers->isDeclarantGamma()) {

            return false;
        }

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_GAMMA)) {

            return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        return true;
    }

}
