<?php

/**
 * tiers actions.
 *
 * @package    civa
 * @subpackage tiers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tiersActions extends EtapesActions {
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeLogin(sfWebRequest $request) {
        if ($this->getUser()->isAuthenticated()) {
            $this->redirect('@mon_espace_civa');
        }elseif($request->getParameter('ticket')) {
            error_reporting(E_ALL);
            require_once(sfConfig::get('sf_lib_dir').'/vendor/phpCAS/CAS.class.php');
            phpCAS::client(CAS_VERSION_2_0,sfConfig::get('app_cas_domain'), sfConfig::get('app_cas_port'), sfConfig::get('app_cas_path'), false);

            phpCAS::setNoCasServerValidation();
            
            $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} about to force auth');
            phpCAS::forceAuthentication();
            $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} auth is good');
            $this->getUser()->signInWithCas(phpCAS::getUser());
            if ($this->getUser()->isDeclarant()) {
                $this->redirect('@mon_espace_civa');
            } elseif ($this->getUser()->isNonDeclarant()) {
                $this->redirect('@mon_espace_civa_non_declarant');
            } elseif($this->getUser()->isAdmin()) {
                $this->redirect('@login_admin');
            }
        }else {
            $url = sfConfig::get('app_cas_url').'/login?service='.$request->getUri();
            $this->redirect($url);
        }
    }

    public function executeLoginAdmin(sfWebRequest $request) {
        $this->getUser()->removeTiers();
        $this->form = new LoginForm(null, array('need_create' => false));
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signIn($this->form->getValue('tiers'));
                $this->redirect('@mon_espace_civa');
            }
        }
    }

    public function executeLogout(sfWebRequest $request) {
        require_once(sfConfig::get('sf_lib_dir').'/vendor/phpCAS/CAS.class.php');
        $this->getUser()->signOut();
        $url = 'http://'.$request->getHost();
        error_reporting(E_ALL);
        phpCAS::client(CAS_VERSION_2_0,sfConfig::get('app_cas_domain'), sfConfig::get('app_cas_port'), sfConfig::get('app_cas_path'), false);
        phpCAS::logoutWithRedirectService($url);
        $this->redirect($url);
    }
    /**
     *
     * @param sfWebRequest $request
     */

    public function executeExploitationAdministratif(sfWebRequest $request) {

        $this->setCurrentEtape('exploitation_administratif');
        $this->help_popup_action = "help_popup_exploitation_administratif";

        $this->forwardUnless($this->tiers = $this->getUser()->getTiers(), 'declaration', 'monEspaceciva');

        $this->form_gest = new TiersExploitantForm($this->getUser()->getTiers()->getExploitant());
        $this->form_gest_err = 0;
        $this->form_expl = new TiersExploitationForm($this->getUser()->getTiers());
        $this->form_expl_err = 0;

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($request->getParameter('gestionnaire')) {
                $this->form_gest->bind($request->getParameter($this->form_gest->getName()));
                if ($this->form_gest->isValid()) {
                    $this->form_gest->save();
                } else {
                    $this->form_gest_err = 1;
                }
            }
            if ($request->getParameter('exploitation')) {
                $this->form_expl->bind($request->getParameter($this->form_expl->getName()));
                if ($this->form_expl->isValid()) {

                    $tiers = $this->form_expl->save();
                    $ldap = new ldap();

                    if($tiers && $ldap) {

                        /*$values['nom'] = $tiers->nom;
                        $values['adresse'] = $tiers->siege->adresse;
                        $values['code_postal'] = $tiers->siege->code_postal;
                        $values['ville'] = $tiers->siege->commune;*/
                        $ldap->ldapModify($this->getUser()->getTiers());
                    }

                } else {
                    $this->form_expl_err = 1;
                }
            }
            if (!$this->form_gest_err && !$this->form_expl_err) {
                $dr = $this->getUser()->getDeclaration();
                $dr->declarant->nom = $this->tiers->exploitant->nom;
                $dr->declarant->telephone = $this->tiers->exploitant->telephone;
                $dr->declarant->email = $this->tiers->email;
                $dr->save();
                $this->redirectByBoutonsEtapes();
            }
        }
    }

    public function executeMonEspaceCivaNonDeclarant(sfWebRequest $request) {
        $this->tiers = $this->getUser()->getTiers();
        if ($request->isMethod(sfWebRequest::POST) && $request->getParameter('gamma_bouton')) {
            $this->redirect('http://qualif.gamma.vinsalsace.pro/');
        }
    }

    public function executeGamma(sfWebRequest $request) {
        if (($this->getUser()->isAdmin() || $this->getUser()->getTiers()->hasNoAssices()) && $request->isMethod(sfWebRequest::POST)) {
            $this->redirect('http://qualif.gamma.vinsalsace.pro/');
        }
    }

}
