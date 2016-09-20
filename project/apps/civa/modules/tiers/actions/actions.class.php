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
        $this->compte = $this->getUser()->getCompte();
        $this->societe = $this->compte->getSociete();
    	$not_uniq = 0;
    	$etablissements = array();
        $etablissementsObject = $this->societe->getEtablissementsObject();
        if (count($etablissementsObject) >= 1) {
    	    foreach ($etablissementsObject as $e) {
                if (isset($etablissements[$e->getFamille()])) {
                  $not_uniq = 1;
                  continue;
                }
                $etablissements[$e->famille] = $e;
            }

    	    if (!$not_uniq) {
                $this->getUser()->signInTiers(array_values($etablissements));

                $referer = $this->getUser()->getFlash('referer');
                if($referer && $referer != $request->getUri() && preg_replace("/\/$/", "", $referer) != $request->getUriPrefix()) {
                    return $this->redirect($referer);
                }

                return $this->redirect("mon_espace_civa", array('identifiant' => $this->compte->getIdentifiant()));
    	    }
        }

        $this->form = new TiersLoginForm($this->compte);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $t = $this->form->process();
                $tiers[$t->type] = $t;
                $this->getUser()->signInTiers(array_values($tiers));

            }
        }
    }

    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_mon_espace_civa";

        $this->compte = CompteClient::getInstance()->findByLogin($request->getParameter('identifiant'));

        if($this->getUser()->isSimpleOperateur() && TiersSecurity::getInstance($this->compte)->isAuthorized(TiersSecurity::DR) && CurrentClient::getCurrent()->isDREditable()) {

            return $this->redirect('mon_espace_civa_dr');
        }
        if($request->getParameter('identifiant') != $this->getUser()->getCompte()->getIdentifiant()){
          return $this->redirect('mon_espace_civa',array("identifiant" => $this->getUser()->getCompte()->getIdentifiant()));
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

        $blocs = TiersSecurity::getInstance($this->compte)->getBlocs();

        $this->nb_blocs = count($blocs);

        if($this->nb_blocs == 1) {
            foreach($blocs as $droit => $url) {
                if(is_array($url)) {
                    return $this->redirect($url[0], $url[1]);
                }
                return $this->redirect($url);
            }
        }
    }

    public function executeMonEspaceDR(sfWebRequest $request) {
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->compte = $this->etablissement->getSociete()->getContact();
        $this->campagne = CurrentClient::getCurrent()->campagne;
        $this->dr = DRClient::getInstance()->retrieveByCampagneAndCvi($this->etablissement->getIdentifiant(), $this->campagne);
        $this->secureTiers(TiersSecurity::DR);

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceDRAcheteur(sfWebRequest $request) {
        $this->secureTiers(TiersSecurity::DR_ACHETEUR);

        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->formUploadCsv = new UploadCSVForm();
    }

    public function executeMonEspaceDS(sfWebRequest $request) {
        $droits = array();
        $this->type_ds = $request->getParameter("type");
        $this->etablissement = $this->getRoute()->getEtablissement();
        $this->compte = $this->etablissement->getSociete()->getContact();
        if($this->type_ds == DSCivaClient::TYPE_DS_NEGOCE) {
               $droits[] = TiersSecurity::DS_NEGOCE;
        } elseif($this->type_ds == DSCivaClient::TYPE_DS_PROPRIETE) {
               $droits[] = TiersSecurity::DS_PROPRIETE;
        } else {

            return $this->forward404();
        }

        $this->secureTiers($droits);

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceVrac(sfWebRequest $request) {
        $this->compte = $this->getRoute()->getCompte();
        $this->etablissements = VracClient::getInstance()->getEtablissements($this->compte->getSociete());
        $this->secureTiers(TiersSecurity::VRAC);


        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    public function executeMonEspaceGamma(sfWebRequest $request) {
        $this->compte = $this->getRoute()->getCompte();
        $this->secureTiers(TiersSecurity::GAMMA);

        $this->help_popup_action = "help_popup_mon_espace_civa";
    }

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeDelegation(sfWebRequest $request) {

        $this->forward404Unless($this->getUser()->hasCredential('delegation'));
        $this->compte = $this->getUser()->getCompte();
        $this->formDelegation = new DelegationLoginForm($this->getUser()->getCompte());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->formDelegation->bind($request->getParameter($this->formDelegation->getName()));

            if ($this->formDelegation->isValid()) {
                $this->getUser()->signInCompteUsed($this->formDelegation->process());
                $this->redirect('@tiers');
            }
        }

        $this->executeMonEspaceDRAcheteur($request);
        $this->setTemplate("monEspaceDRAcheteur");
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
        $this->redirect('@mon_espace_civa');
    }

    protected function secureTiers($droits) {
        if(!TiersSecurity::getInstance($this->compte)->isAuthorized($droits)) {

            return $this->forwardSecure();
        }
    }

    protected function forwardSecure()
    {
        $this->context->getController()->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));

        throw new sfStopException();
    }


}
