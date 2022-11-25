<?php

/**
 * tiers actions.
 *
 * @package    civa
 * @subpackage tiers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tiersActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeLogin(sfWebRequest $request) {
        $this->getUser()->signOutTiers();
        $compte = $this->getUser()->getCompte();
        $this->getUser()->signInTiers($compte->getSociete());

        $referer = $this->getUser()->getFlash('referer');
        if($referer && $referer != $request->getUri() && preg_replace("/\/$/", "", $referer) != $request->getUriPrefix()) {
            return $this->redirect($referer);
        }

        return $this->redirect("mon_espace_civa", array('identifiant' => $compte->getIdentifiant()));
    }

    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_mon_espace_civa";

        $this->secure();

        $this->compte = $this->getRoute()->getCompte();

        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->nb_blocs = count($this->blocs);

        if(count($this->blocs) == 1) {
            foreach ($this->blocs as $url) {

                return $this->redirect($url);
            }
        }

        $this->vracs = array(
            'CONTRAT_A_TERMINER' => 0,
            'CONTRAT_A_SIGNER' => 0,
            'CONTRAT_EN_ATTENTE_SIGNATURE' => 0,
            'CONTRAT_A_ENLEVER' => 0,
        );
        $tiers = VracClient::getInstance()->getEtablissements($this->compte->getSociete());
        $vracs = VracTousView::getInstance()->findSortedByDeclarants($tiers);
        foreach($vracs as $vrac) {
            $item = $vrac->value;
            if($item->papier) {
                continue;
            }

            if($item->statut == Vrac::STATUT_CREE && $item->is_proprietaire) {
               $this->vracs['CONTRAT_A_TERMINER'] += 1;
            }

            if($item->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT) {
                if(array_key_exists($item->soussignes->vendeur->identifiant, $tiers) && !$item->soussignes->vendeur->date_validation) {
                    $this->vracs['CONTRAT_A_SIGNER'] += 1;
                }
                if(array_key_exists($item->soussignes->acheteur->identifiant, $tiers) && !$item->soussignes->acheteur->date_validation) {
                    $this->vracs['CONTRAT_A_SIGNER'] += 1;
                }
                if(array_key_exists($item->soussignes->mandataire->identifiant, $tiers) && !$item->soussignes->mandataire->date_validation) {
                    $this->vracs['CONTRAT_A_SIGNER'] += 1;
                }
                if($item->is_proprietaire) {
                    $this->vracs['CONTRAT_EN_ATTENTE_SIGNATURE'] += 1;
                }
            }

            if($item->is_proprietaire && ($item->statut == Vrac::STATUT_VALIDE || $item->statut == Vrac::STATUT_ENLEVEMENT)) {
                $this->vracs['CONTRAT_A_ENLEVER'] += 1;
            }
        }

        $this->drNeedToDeclare = false;
        if($this->compte->hasDroit(Roles::TELEDECLARATION_DR) && DRClient::getInstance()->isTeledeclarationOuverte()) {
            $dr = DRClient::getInstance()->retrieveByCampagneAndCvi(DRClient::getInstance()->getEtablissement($this->compte->getSociete())->getIdentifiant(), CurrentClient::getCurrent()->campagne);
            $this->drNeedToDeclare = !$dr || !$dr->isValideeTiers();
        }
    }

    public function executeNav(sfWebRequest $request) {
        $compte = null;
        if($request->getParameter('compte')) {
            $compte = CompteClient::getInstance()->findByLogin($request->getParameter('compte'));
        }

	$isAdmin = $request->getParameter('isAdmin', false);
        if(!$compte) {
            return $this->renderPartial("tiers/ongletsAdmin", array("active" => $request->getParameter('active'), 'isAdmin' => $isAdmin, 'absolute' => true));
        }

        $blocs = $this->buildBlocs($compte, $isAdmin);

        return $this->renderPartial("tiers/onglets", array("compte" => $compte, 'isAdmin' => $isAdmin, "blocs" => $blocs, "active" => $request->getParameter('active'), 'absolute' => true));
    }

    protected function buildBlocs($compte, $isAdmin = false) {
        $blocs = array();
        if($compte->hasDroit(Roles::TELEDECLARATION_DR)) {
            $blocs[Roles::TELEDECLARATION_DR] = $this->generateUrl('mon_espace_civa_dr_compte', $compte);
        }
        $url_drm = sfConfig::get("app_giilda_url_drm");
        if($compte->hasDroit(Roles::TELEDECLARATION_DRM) && $url_drm) {
            foreach($compte->getSociete()->getEtablissementsObject(true, true) as $etablissement) {
                if($etablissement->hasDroit(Roles::TELEDECLARATION_DRM)) {
                    $blocs[Roles::TELEDECLARATION_DRM] = sprintf($url_drm, $etablissement->identifiant);
                    break;
                }
            }

        }
        $etablissement = $compte->getSociete()->getEtablissementPrincipal();

        $url_compte = sfConfig::get("app_giilda_url_compte");
        if($isAdmin && $url_compte && preg_match('/(societe|etablissement|compte)/', $this->getRequest()->getParameter('active'))) {
            $blocs[Roles::CONTACT] = sfConfig::get("app_giilda_url_compte_admin");
        } elseif($isAdmin && $url_compte) {
            $blocs[Roles::CONTACT] = sprintf($url_compte, $etablissement->identifiant);
        }

        $url_facture = sfConfig::get("app_giilda_url_facture");
        if($url_facture && $this->getRequest()->getParameter('active') == 'facture' && $isAdmin) {
            $blocs[Roles::FACTURE] = sfConfig::get("app_giilda_url_facture_admin");
        } elseif($url_facture) {
            $blocs[Roles::FACTURE] = sprintf($url_facture, $compte->getSociete()->identifiant);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)) {
            $blocs[Roles::TELEDECLARATION_DR_ACHETEUR] = $this->generateUrl('mon_espace_civa_dr_acheteur_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_GAMMA)) {
            $blocs[Roles::TELEDECLARATION_GAMMA] = $this->generateUrl('mon_espace_civa_gamma_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_VRAC_CREATION)) {
            $blocs[Roles::TELEDECLARATION_VRAC] = $this->generateUrl('mon_espace_civa_vrac_compte', $compte);
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_VRAC) && !isset($blocs[$compte->hasDroit(Roles::TELEDECLARATION_VRAC)])) {
            $tiersVrac = VracClient::getInstance()->getEtablissements($compte->getSociete());

            if($tiersVrac instanceof sfOutputEscaperArrayDecorator) {
                $tiersVrac = $tiersVrac->getRawValue();
            }

            if(count(VracTousView::getInstance()->findSortedByDeclarants($tiersVrac))) {
                $blocs[Roles::TELEDECLARATION_VRAC] = $this->generateUrl('mon_espace_civa_vrac_compte', $compte);
            }

        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DS_PROPRIETE)) {
            $blocs[Roles::TELEDECLARATION_DS_PROPRIETE] = $this->generateUrl('mon_espace_civa_ds_compte', array('sf_subject' => $compte, 'type' => DSCivaClient::TYPE_DS_PROPRIETE));
        }

        if($compte->hasDroit(Roles::TELEDECLARATION_DS_NEGOCE)) {
            $blocs[Roles::TELEDECLARATION_DS_NEGOCE] = $this->generateUrl('mon_espace_civa_ds_compte', array('sf_subject' => $compte, 'type' => DSCivaClient::TYPE_DS_NEGOCE));
        }

        return $blocs;
    }

    public function executeMonEspaceCompteDR(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR);
        $compte = $this->getRoute()->getCompte();

        return $this->redirect('mon_espace_civa_dr', DRClient::getInstance()->getEtablissement($compte->getSociete()));
    }

    public function executeMonEspaceDR(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR);
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->compte = $this->etablissement->getSociete()->getContact();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->campagne = CurrentClient::getCurrent()->campagne;
        $this->dr = DRClient::getInstance()->retrieveByCampagneAndCvi($this->etablissement->getIdentifiant(), $this->campagne);

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceCompteDRAcheteur(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR_ACHETEUR);
        $compte = $this->getRoute()->getCompte();

        if($this->getUser()->hasFlash('form_error')) {
            $this->getUser()->setFlash('form_error', $this->getUser()->getFlash('form_error'));
        }

        return $this->redirect('mon_espace_civa_dr_acheteur', DRClient::getInstance()->getEtablissementAcheteur($compte->getSociete()));
    }

    public function executeMonEspaceDRAcheteur(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR_ACHETEUR);
        $this->compte = $this->getUser()->getCompte();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->etablissement = $this->getRoute()->getEtablissement();

        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->formUploadCsv = new UploadCSVForm();
    }

    public function executeMonEspaceCompteProduction(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR_ACHETEUR);
        $compte = $this->getRoute()->getCompte();

        if($this->getUser()->hasFlash('form_error')) {
            $this->getUser()->setFlash('form_error', $this->getUser()->getFlash('form_error'));
        }

        return $this->redirect('mon_espace_civa_production', DRClient::getInstance()->getEtablissementAcheteur($compte->getSociete()));
    }

    public function executeMonEspaceProduction(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_DR_ACHETEUR);
        $this->compte = $this->getUser()->getCompte();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->sv = SVClient::getInstance()->findByIdentifiantAndCampagne($this->etablissement->getIdentifiant(), '2021');
        if ($this->sv) {
            $this->form = new SVStartupForm($this->sv);
            $this->formaction = 'mon_espace_civa_production';
        } else {
            // redirigé vers sv_etablissement
            $this->form = new SVCreationForm($this->etablissement->identifiant, "2021");
            $this->formaction = 'sv_etablissement';
        }

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                if ($this->form->save() === false) {
                    SVClient::getInstance()->delete($this->sv);
                    return $this->redirect('mon_espace_civa_production', ['identifiant' => $this->etablissement->identifiant]);
                } else {
                    return $this->redirect('sv_etablissement');
                }
            }
        }
    }

    public function executeMonEspaceCompteDS(sfWebRequest $request) {
        if($request->getParameter('type') == DSCivaClient::TYPE_DS_PROPRIETE) {
            $this->secure(Roles::TELEDECLARATION_DS_PROPRIETE);
        }

        if($request->getParameter('type') == DSCivaClient::TYPE_DS_NEGOCE) {
            $this->secure(Roles::TELEDECLARATION_DS_NEGOCE);
        }
        $compte = $this->getRoute()->getCompte();

        return $this->redirect('mon_espace_civa_ds', array('sf_subject' => DSCivaClient::getInstance()->getEtablissement($compte->getSociete(), $request->getParameter('type')), 'type' => $request->getParameter('type')));
    }

    public function executeMonEspaceDS(sfWebRequest $request) {
        if($request->getParameter('type') == DSCivaClient::TYPE_DS_PROPRIETE) {
            $this->secure(Roles::TELEDECLARATION_DS_PROPRIETE);
        }

        if($request->getParameter('type') == DSCivaClient::TYPE_DS_NEGOCE) {
            $this->secure(Roles::TELEDECLARATION_DS_NEGOCE);
        }

        $this->type_ds = $request->getParameter("type");
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->compte = $this->etablissement->getSociete()->getContact();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceCompteVrac(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_VRAC);
        $compte = $this->getRoute()->getCompte();

        return $this->redirect('mon_espace_civa_vrac', $compte);
    }

    public function executeMonEspaceVrac(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_VRAC);
        $this->compte = $this->getRoute()->getCompte();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->etablissements = VracClient::getInstance()->getEtablissements($this->compte->getSociete());

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceCompteGamma(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_GAMMA);
        $compte = $this->getRoute()->getCompte();

        return $this->redirect('mon_espace_civa_gamma', GammaClient::getInstance()->getEtablissement($compte));
    }

    public function executeMonEspaceGamma(sfWebRequest $request) {
        $this->secure(Roles::TELEDECLARATION_GAMMA);
        $this->compte = $this->getUser()->getCompte();
        $this->blocs = $this->buildBlocs($this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->isInscrit = GammaClient::getInstance()->findByEtablissement($this->etablissement);

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeDelegation(sfWebRequest $request) {
        $this->forward404Unless($this->getUser()->hasCredential(myUser::CREDENTIAL_DELEGATION));
        $this->compte = $this->getUser()->getCompte();
        $this->formDelegation = new DelegationLoginForm($this->getUser()->getCompte());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->formDelegation->bind($request->getParameter($this->formDelegation->getName()));

            if ($this->formDelegation->isValid()) {
                $this->getUser()->signInCompteUsed($this->formDelegation->process());
                $this->redirect('tiers');
            }
        }

        $this->getUser()->setFlash('form_error', "Ce CVI n'existe pas");
        return $this->redirect('mon_espace_civa_dr_acheteur_compte', $this->getUser()->getCompte());
    }


    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeMigrationCompte(sfWebRequest $request) {

        $this->form = new NewCviForm($this->getUser()->getCompte());
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $postParameters = $request->getPostParameters();
                $nouveau_cvi = $postParameters["new_cvi"]["nouveau_cvi"];
                $compte = $this->getUser()->getCompte();
                $new_compte = new MigrationCompte($compte, $nouveau_cvi);
                $this->success = $new_compte->process();
            }
        }
    }

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeSignoutByCompteDelegue(sfWebRequest $request) {

        $login_current_user = $this->getUser()->getCompte(myUser::NAMESPACE_COMPTE_AUTHENTICATED)->login;
        $this->getUser()->signIn($login_current_user);
        $this->redirect('mon_espace_civa', $this->getUser()->getCompte());
    }

    protected function secure($droits = array()) {
        if($this->getRoute() instanceof CompteRoute) {
            if(!CompteSecurity::getInstance($this->getRoute()->getCompte())->isAuthorized($droits)) {
                return $this->forwardSecure();
            }
        }

        if($this->getRoute() instanceof EtablissementRoute) {
            if(!EtablissementSecurity::getInstance($this->getRoute()->getEtablissement())->isAuthorized($droits)) {
                return $this->forwardSecure();
            }
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }


}
