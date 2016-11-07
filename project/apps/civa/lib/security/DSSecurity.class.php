<?php

class DSSecurity implements SecurityInterface {

    const CONSULTATION = 'CONSULTATION';
    const CREATION = 'CREATION';
    const EDITION = 'EDITION';

    protected $ds;
    protected $type_ds;
    protected $etablissement;

    public static function getInstance($etablissement, $ds_principale = null, $type_ds = null) {

        return new DSSecurity($etablissement, $ds_principale, $type_ds);
    }

    public function __construct($etablissement, $ds_principale = null, $type_ds = null) {
        $this->etablissement = $etablissement;
        $this->ds = $ds_principale;
        if($this->ds) {
            $type_ds = $ds_principale->type_ds;
        }
        if(!$type_ds) {

            throw new sfException("Le type de la DS \"".$type_ds."\" n'est pas comnnu");
        }
        $this->type_ds = $type_ds;
    }

    public function getUser() {

        return sfContext::getInstance()->getUser();
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(!$this->etablissement) {

            return false;
        }

        if($this->type_ds == DSCivaClient::TYPE_DS_PROPRIETE && !EtablissementSecurity::getInstance($this->etablissement)->isAuthorized(Roles::TELEDECLARATION_DS_PROPRIETE)) {
            return false;
        }

        if($this->type_ds == DSCivaClient::TYPE_DS_NEGOCE && !EtablissementSecurity::getInstance($this->etablissement)->isAuthorized(Roles::TELEDECLARATION_DS_NEGOCE)) {
            return false;
        }

        if(!$this->etablissement->hasLieuxStockage() && !$this->etablissement->isAjoutLieuxDeStockage()) {
            return false;
        }

        if($this->etablissement && $this->ds && $this->etablissement->getIdentifiant() != $this->ds->identifiant && $this->etablissement->getCvi() != $this->ds->identifiant) {
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

        if(in_array(self::CREATION, $droits) && $this->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::CREATION, $droits) && !$this->compte->isDsEditable($this->type_ds)) {

            return false;
        }


        if(in_array(self::CREATION, $droits)) {
            return true;
        }



        /*** EDITION ***/

        if(in_array(self::EDITION , $droits) && !$this->ds) {
            return false;
        }

        if(in_array(self::EDITION , $droits) && $this->ds->isValideeCiva()) {

            return false;
        }

        if(in_array(self::EDITION , $droits) && sfContext::getInstance()->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN)) {

            return true;
        }

        if(in_array(self::EDITION , $droits) && $this->ds->isValideeTiers()) {

            return false;
        }

        if(in_array(self::EDITION , $droits) && sfContext::getInstance()->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {

            return true;
        }

        if(in_array(self::EDITION , $droits) && !$this->getUser()->isDsEditable($this->type_ds)) {

            return false;
        }

        return true;
    }

}
