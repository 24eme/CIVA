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

    public function autoSignin($compte)
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

    public static function buildBlocs($sfaction, $compte, $isAdmin = false, $etablissement = null) {
        $blocs = array();
        if($compte->hasDroit(Roles::TELEDECLARATION_DR)) {
            $blocs[Roles::TELEDECLARATION_DR] = $sfaction->generateUrl('mon_espace_civa_dr_compte', $compte);
        }
        $url_drm = sfConfig::get("app_giilda_url_drm");
        $societe = $compte->getSociete();
        if($compte->hasDroit(Roles::TELEDECLARATION_DRM) && $url_drm) {
            if($etablissement) {
                $blocs[Roles::TELEDECLARATION_DRM] = sprintf($url_drm, $etablissement->identifiant);
            } else {
                foreach($societe->getEtablissementsObject(true, true) as $e) {
                    if($e->hasDroit(Roles::TELEDECLARATION_DRM) && $e->getMasterCompte()->hasDroit(Roles::TELEDECLARATION_DRM)) {
                        $blocs[Roles::TELEDECLARATION_DRM] = sprintf($url_drm, $e->identifiant);
                        break;
                    }
                }
            }
        }
        if($isAdmin && $url_drm && preg_match('/(drm)/', $sfaction->getRequest()->getParameter('active'))) {
            $blocs[Roles::TELEDECLARATION_DRM] = sfConfig::get("app_giilda_url_drm_admin");
        }
        if ($societe && !$etablissement) {
            $etablissement = $societe->getEtablissementPrincipal();
        }

        $url_compte = sfConfig::get("app_giilda_url_compte");
        if($isAdmin && $url_compte && preg_match('/(societe|etablissement|compte)/', $sfaction->getRequest()->getParameter('active'))) {
            $blocs[Roles::CONTACT] = sfConfig::get("app_giilda_url_compte_admin");
        } elseif($isAdmin && $url_compte) {
            $blocs[Roles::CONTACT] = sprintf($url_compte, $societe->identifiant);
        }

        $url_facture = sfConfig::get("app_giilda_url_facture");
        if($url_facture && $sfaction->getRequest()->getParameter('active') == 'facture' && $isAdmin) {
            $blocs[Roles::FACTURE] = sfConfig::get("app_giilda_url_facture_admin");
        } elseif($url_facture) {
            $blocs[Roles::FACTURE] = sprintf($url_facture, $societe->identifiant);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)) {
            $blocs[Roles::TELEDECLARATION_DR_ACHETEUR] = $sfaction->generateUrl('mon_espace_civa_dr_acheteur_compte', $compte);
            $blocs[Roles::TELEDECLARATION_PRODUCTION] = $sfaction->generateUrl('mon_espace_civa_production_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_GAMMA)) {
            $blocs[Roles::TELEDECLARATION_GAMMA] = $sfaction->generateUrl('mon_espace_civa_gamma_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_VRAC_CREATION)) {
            $blocs[Roles::TELEDECLARATION_VRAC] = $sfaction->generateUrl('mon_espace_civa_vrac_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_VRAC) && !isset($blocs[$compte->hasDroit(Roles::TELEDECLARATION_VRAC)])) {
            $tiersVrac = VracClient::getInstance()->getEtablissements($societe);

            if($tiersVrac instanceof sfOutputEscaperArrayDecorator) {
                $tiersVrac = $tiersVrac->getRawValue();
            }

            if(count(VracTousView::getInstance()->findSortedByDeclarants($tiersVrac))) {
                $blocs[Roles::TELEDECLARATION_VRAC] = $sfaction->generateUrl('mon_espace_civa_vrac_compte', $compte);
            }

        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)) {
            $blocs[Roles::TELEDECLARATION_DS_PROPRIETE] = $sfaction->generateUrl('mon_espace_civa_ds_compte', array('sf_subject' => $compte, 'type' => DSCivaClient::TYPE_DS_PROPRIETE));
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)) {
            $blocs[Roles::TELEDECLARATION_DS_NEGOCE] = $sfaction->generateUrl('mon_espace_civa_ds_compte', array('sf_subject' => $compte, 'type' => DSCivaClient::TYPE_DS_NEGOCE));
        }

        return $blocs;
    }

}
