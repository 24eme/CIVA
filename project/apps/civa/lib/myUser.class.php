<?php

/**
 * Description of myUser
 *
 * @author vince
 */
class myUser extends DeclarationSecurityUser {
    const SESSION_COMPTE_LOGIN = "COMPTE_LOGIN";
    const SESSION_COMPTE_DOC = "COMPTE_DOC_ID";
    const SESSION_USURPATION_URL_BACK = "USURPATION_URL_BACK";
    const NAMESPACE_COMPTE = "COMPTE";
    const NAMESPACE_COMPTE_ORIGIN = "COMPTE_ORIGIN";
    const CREDENTIAL_ADMIN = "admin";
    
    public function hasTeledeclaration() {
        return $this->isAuthenticated() && $this->getCompte() && $this->hasCredential(Roles::TELEDECLARATION);
    }

    public function hasTeledeclarationDrm() {
        return $this->hasTeledeclaration() && $this->hasCredential(Roles::TELEDECLARATION_DRM);
    }

    public function hasOnlyCredentialDRM() {
        return $this->hasCredential(Roles::ROLEDRM) && $this->hasCredential(Roles::DRM);
    }

    public function isUsurpationCompte() {

        return $this->getAttribute(self::SESSION_COMPTE_LOGIN, null, self::NAMESPACE_COMPTE) != $this->getAttribute(self::SESSION_COMPTE_LOGIN, null, self::NAMESPACE_COMPTE_ORIGIN);
    }

    public static function autoSignin($compte)
    {
         if(! $this->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
             return;
         }

         $societe = $compte->getSociete();

         if($societe->_id == $this->getCompte()->id_societe) {
             return;
         }

         $this->signInCompteUsed($compte);
         $this->signOutTiers();
         $this->signInTiers($societe);
    }

}
