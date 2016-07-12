<?php

class VracSecurity implements SecurityInterface {

    const DECLARANT = 'DECLARANT';
    const CONSULTATION = 'CONSULTATION';
    const CREATION = 'CREATION';
    const SUPPRESSION = 'SUPPRESSION';
    const EDITION = 'EDITION';
    const SIGNATURE = 'SIGNATURE';
    const ENLEVEMENT = 'ENLEVEMENT';
    const CLOTURE = 'CLOTURE';

    protected $vrac;
    protected $myUser;
    protected $tiers;

    public static function getInstance($myUser, $vrac = null) {

        return new VracSecurity($myUser, $vrac);
    }

    public function __construct($myUser, $vrac = null) {
        $this->myUser = $myUser;
        $this->vrac = $vrac;
        $this->tiers = $this->myUser->getDeclarantsVrac();
    }

    public function isAuthorized($droits) {
        foreach($this->tiers as $t) {
            if($this->isAuthorizedTiers($t, $droits)) {
                return true;
            }
        }
        return false;
    }

    public function isAuthorizedTiers($tiers, $droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        /*if(!$tiers->isDeclarantContrat()) {

            return false;
        }*/

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_VRAC_SIGNATURE) && !$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_VRAC_RESPONSABLE)) {

            //return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        /*** CREATION ***/

        /*if(in_array(self::CREATION, $droits) && !$tiers->isDeclarantContratForResponsable()) {

            return false;
        }*/

        if(in_array(self::CREATION, $droits)) {

            return true;
        }

        /*** EDITION ***/

        if(!$this->vrac) {

            return false;
        }

        if(!$this->vrac->isActeur($tiers->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isProprietaire($tiers->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isBrouillon()) {

            return false;
        }

        /*** SIGNATURE ***/

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->hasSigne($tiers->_id)) {

            return false;
        }

        /*** SUPPRESSION ***/

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isProprietaire($tiers->_id)) {

            return false;
        }

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isSupprimable()) {

            return false;
        }

        /*** ENLEVEMENT ***/

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isProprietaire($tiers->_id)) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && $this->vrac->isCloture()) {

            return false;
        }

        /*** CLOTURE ***/

        if(in_array(self::CLOTURE, $droits) && !$this->vrac->isProprietaire($tiers->_id)) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && $this->vrac->isCloture()) {

            return false;
        }

        return true;
    }

}
