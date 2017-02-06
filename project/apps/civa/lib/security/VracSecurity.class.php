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
    const FORCE_CLOTURE = 'FORCE_CLOTURE';

    protected $vrac;
    protected $compte;
    protected $etablissements;

    public static function getInstance($compte, $vrac = null) {

        return new VracSecurity($compte, $vrac);
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }

    public function __construct($compte, $vrac = null) {
        $this->compte = $compte;
        if(!$this->compte) {

            throw new sfException("Le compt est nul");
        }
        $this->vrac = $vrac;
        $this->etablissements = VracClient::getInstance()->getEtablissements($this->compte->getSociete());
    }

    public function isAuthorized($droits) {
        foreach($this->etablissements as $etablissement) {
            if($this->isAuthorizedTiers($etablissement, $droits)) {
                return true;
            }
        }
        return false;
    }

    public function isAuthorizedTiers($etablissement, $droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        /*** DECLARANT ***/

        if(!EtablissementSecurity::getInstance($etablissement)->isAuthorized(Roles::TELEDECLARATION_VRAC)) {

            return false;
        }

        if(in_array(self::DECLARANT, $droits)) {

            return true;
        }

        /*** CREATION ***/


        if(in_array(self::CREATION, $droits) && !$etablissement->hasDroit(Roles::TELEDECLARATION_VRAC_CREATION)) {

            return false;
        }

        if(in_array(self::CREATION, $droits)) {

            return true;
        }

        /*** EDITION ***/

        if(!$this->vrac) {

            return false;
        }

        if(!$this->vrac->isActeur($etablissement->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isProprietaire($etablissement->_id)) {

            return false;
        }

        if(in_array(self::EDITION, $droits) && !$this->vrac->isBrouillon()) {

            return false;
        }

        /*** SIGNATURE ***/

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::SIGNATURE, $droits) && $this->vrac->hasSigne($etablissement->_id)) {

            return false;
        }

        /*** SUPPRESSION ***/

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isProprietaire($etablissement->_id)) {

            return false;
        }

        if(in_array(self::SUPPRESSION, $droits) && !$this->vrac->isSupprimable()) {

            return false;
        }

        /*** ENLEVEMENT ***/

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isProprietaire($etablissement->_id)) {
            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && !$this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::ENLEVEMENT, $droits) && $this->vrac->isCloture()) {

            return false;
        }

        /*** CLOTURE ***/

        if(in_array(self::CLOTURE, $droits) && !$this->vrac->isProprietaire($etablissement->_id)) {

            return false;
        }

        if(in_array(self::CLOTURE, $droits) && !$this->vrac->isValide()) {

            return false;
        }

        if(in_array(self::CLOTURE, $droits) && $this->vrac->isCloture()) {

            return false;
        }

        /*** FORCE CLOTURE ***/

        if(in_array(self::FORCE_CLOTURE, $droits) && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {

            return false;
        }

        if(in_array(self::FORCE_CLOTURE, $droits) && !$this->vrac->canForceClotureContrat()) {

            return false;
        }

        return true;
    }

}
