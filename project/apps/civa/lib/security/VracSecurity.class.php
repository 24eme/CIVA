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

    protected $instance;
    protected $vrac;
    protected $myUser;

    public static function getInstance($myUser, $vrac = null) {

        return new VracSecurity($myUser, $vrac);
    }

    public function __construct($myUser, $vrac = null) {
        $this->myUser = $myUser;
        $this->vrac = $vrac;
        $this->tiers = $this->myUser->getDeclarant();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!$this->tiers->isDeclarantContrat()) {
            
            return false;
        }

        if(!$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_VRAC_SIGNATURE) && !$this->myUser->getCompte()->hasDroit(_CompteClient::DROIT_VRAC_RESPONSABLE)) {
            
            return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        /*** CREATION ***/

        if(in_array(self::CREATION, $droits) && !$this->tiers->isDeclarantContratForResponsable()) {

            return false;
        }

        if(in_array(self::CREATION, $droits)) {

            return true;
        }

        /*** EDITION ***/

        if(!$this->vrac) {

            return false;
        }

        if(!$this->vrac->isActeur($this->tiers->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isProprietaire($this->tiers->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isBrouillon()) {

            return false;
        }

        /*** SIGNATURE ***/

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->hasSigne($this->tiers->_id)) {

            return false;
        }

        /*** SUPPRESSION ***/

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isProprietaire($this->tiers->_id)) {

            return false;
        }

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isSupprimable()) {

            return false;
        }

        /*** ENLEVEMENT ***/

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isProprietaire($this->tiers->_id)) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && $this->vrac->isCloture()) {

            return false;
        }

        /*** CLOTURE ***/

        if(in_array(self::CLOTURE, $droits) && !$this->vrac->isProprietaire($this->tiers->_id)) {

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