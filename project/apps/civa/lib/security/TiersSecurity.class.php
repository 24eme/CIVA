<?php

class TiersSecurity {

    const DR = 'DR';
    const DR_APPORTEUR = 'DR_APPORTEUR';
    const DS = 'DS';
    const GAMMA = 'GAMMA';
    const VRAC = 'VRAC';

    protected $instance;
    protected $myUser;

    public static function getInstance($myUser) {

        return new TiersSecurity($myUser);
    }

    public function __construct($myUser) {
        $this->myUser = $myUser;
    }

    public function isAuthorized($droits) {
        if(!is_array($droits)) {
            $droits = array($droits);
        }

        if(in_array(self::DR, $droits)) {

            return $this->myUser->hasCredential(myUser::CREDENTIAL_DECLARATION);
        }

        if(in_array(self::DR_APPORTEUR, $droits)) {

            return $this->myUser->hasCredential(myUser::CREDENTIAL_ACHETEUR);
        }

        if(in_array(self::DS, $droits)) {

            return $this->myUser->getTiers()->isDeclarantStock();
        }
        
        if(in_array(self::VRAC, $droits)) {

            return VracSecurity::getInstance($this->myUser, null)->isAuthorized(VracSecurity::DECLARANT);
        }

        if(in_array(self::GAMMA, $droits)) {

            return $this->myUser->hasCredential(myUser::CREDENTIAL_DECLARATION);
        }

        return false;
    }

}