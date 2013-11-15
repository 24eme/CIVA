<?php

/**
 * compte actions.
 *
 * @package    civa
 * @subpackage compte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class compteActions extends sfActions {

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeLogin(sfWebRequest $request) {
        if(sfConfig::has('app_login_no_cas') && sfConfig::get('app_login_no_cas')) {

            return $this->redirect('login_no_cas');
        }

        if ($this->getUser()->isAuthenticated() && $this->getUser()->hasCredential("compte")) {
            
            return $this->redirectAfterLogin($request);
        }
        
        if ($request->getParameter('ticket')) {
            /** CAS * */
            error_reporting(E_ALL);
            require_once(sfConfig::get('sf_lib_dir') . '/vendor/phpCAS/CAS.class.php');
            phpCAS::client(CAS_VERSION_2_0, sfConfig::get('app_cas_domain'), sfConfig::get('app_cas_port'), sfConfig::get('app_cas_path'), false);
            phpCAS::setNoCasServerValidation();
            $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} about to force auth');
            phpCAS::forceAuthentication();
            $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} auth is good');
            /** ***** */
            $this->getUser()->signIn(phpCAS::getUser());

            return $this->redirectAfterLogin($request);
        }

		if(sfConfig::has('app_autologin') && sfConfig::get('app_autologin')) {
			$this->getUser()->signIn(sfConfig::get('app_autologin'));
            
            return $this->redirectAfterLogin($request);
		}	   
		
	    $url = sfConfig::get('app_cas_url') . '/login?service=' . $request->getUri();
	    
        return $this->redirect($url);
    }

    protected function redirectAfterLogin($request) {
        if($request->getParameter('ticket')) {

            $this->getUser()->setFlash('referer', $request->getUri());
        } elseif($request->getReferer()) {

            $this->getUser()->setFlash('referer', $request->getReferer());
        } 

        return $this->redirect('tiers');
    } 

    public function executeLoginNoCas(sfWebRequest $request) {
        if (!(sfConfig::has('app_login_no_cas') && sfConfig::get('app_login_no_cas'))) {
            
            return $this->forward404Unless();
        }

        if($this->getUser()->hasCredential(myUser::CREDENTIAL_COMPTE)) {

            return $this->redirect('@tiers');
        }

        $this->getUser()->signOut();

        $this->form = new AdminCompteLoginForm(null, array('comptes_type' => array('CompteVirtuel'), false));
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signIn($this->form->process()->login);
                $this->redirect('@tiers');
            }
        }
    }
    
    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeLogout(sfWebRequest $request) {
        require_once(sfConfig::get('sf_lib_dir').'/vendor/phpCAS/CAS.class.php');
        $this->getUser()->signOut();
        $url = 'http://'.$request->getHost();
        error_reporting(E_ALL);
        phpCAS::client(CAS_VERSION_2_0,sfConfig::get('app_cas_domain'), sfConfig::get('app_cas_port'), sfConfig::get('app_cas_path'), false);
        if (phpCas::isAuthenticated()) {
            phpCAS::logoutWithRedirectService($url);
        }
        $this->redirect($url);
    }




    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeFirst(sfWebRequest $request) {
        $this->form = new CompteLoginFirstForm();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signInFirst($this->form->getValue('compte'));
                $this->redirect('@compte_creation');
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeCreation(sfWebRequest $request) {
        $this->compte = $this->getUser()->getCompte();
        $this->forward404Unless($this->compte->getStatus() == _Compte::STATUS_NOUVEAU);

        $this->form = new CreationCompteForm($this->compte);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->compte = $this->form->save();
                try {
                    $message = $this->getMailer()->composeAndSend(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"), $this->compte->email, "CIVA - Création de votre compte", "Bonjour " . $this->compte->nom . ",\n\nVotre compte a bien été créé sur le site du CIVA. \n\nCordialement,\n\nLe CIVA");
                    $this->getUser()->setFlash('confirmation', "Votre compte a bien été créé.");
                } catch (Exception $e) {
                    $this->getUser()->setFlash('error', "Problème de configuration : l'email n'a pu être envoyé");
                }
                $this->redirect('@tiers');
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeModificationOublie(sfWebRequest $request) {
        $this->compte = $this->getUser()->getCompte();
        $this->forward404Unless($this->compte->getStatus() == _Compte::STATUS_MOT_DE_PASSE_OUBLIE);

        $this->form = new CompteModificationOublieForm($this->compte);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->compte = $this->form->save();
                try {
                    $message = $this->getMailer()->composeAndSend(array('ne_pas_repondre@civa.fr' => "Webmaster Vinsalsace.pro"), $this->compte->email, "CIVA - Changement de votre mot de passe", "Bonjour " . $this->compte->nom . ",\n\nVotre mot de passe sur le site du CIVA vient d'etre modifié.\n\nCordialement,\n\nLe CIVA");
                    $this->getUser()->setFlash('confirmation', "Votre mot de passe a bien été modifié.");
                } catch (Exception $e) {
                    $this->getUser()->setFlash('error', "Problème de configuration : l'email n'a pu être envoyé");
                }
                $this->redirect('@mon_espace_civa');
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeModification(sfWebRequest $request) {
        $this->compte = $this->getUser()->getCompte();
        $this->forward404Unless(in_array($this->compte->getStatus(), array(_Compte::STATUS_MOT_DE_PASSE_OUBLIE, _Compte::STATUS_INSCRIT)));

        $this->form = new CompteModificationForm($this->compte);
	$this->redirect = $request->getParameter('redirect');

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->compte = $this->form->save();
                $this->getUser()->setFlash('maj', 'Vos identifiants ont bien été mis à jour.');
		if ($this->redirect) {
		  return $this->redirect($this->redirect);
		}
                $this->redirect('@compte_modification');
            }
        }
    }

    public function executeMotDePasseOublieLogin(sfWebRequest $request) {
        $this->forward404Unless($compte = acCouchdbManager::getClient('_Compte')->retrieveByLogin($request->getParameter('login', null)));
        $this->forward404Unless($compte->mot_de_passe == '{OUBLIE}' . $request->getParameter('mdp', null));
        $this->getUser()->signInFirst($compte);
        $this->redirect('@compte_modification_oublie');
    }

    public function executeMotDePasseOublie(sfWebRequest $request) {
        $this->form = new CompteMotDePasseOublieForm();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $compte = $this->form->save();
                $lien = "http://".str_replace("//", "/", str_replace('http://', '', sfConfig::get('app_base_url') . $this->generateUrl("compte_mot_de_passe_oublie_login", array("login" => $compte->login, "mdp" => str_replace("{OUBLIE}", "", $compte->mot_de_passe)))));
                try {
                    $this->getMailer()->composeAndSend(array("ne_pas_repondre@civa.fr" => "Webmaster Vinsalsace.pro"), $compte->email, "CIVA - Mot de passe oublié", "Bonjour " . $compte->nom . ", \n\nVous avez oublié votre mot de passe pour le redéfinir merci de cliquer sur le lien suivant : " . $lien . "\n\nCordialement,\n\nLe CIVA");
                } catch (Exception $e) {
                    $this->getUser()->setFlash('error', "Problème de configuration : l'email n'a pu être envoyé");
                }
                $this->redirect('@compte_mot_de_passe_oublie_confirm');
            }
        }
    }
    
    public function executeMotDePasseOublieConfirm(sfWebRequest $request) {
        
    }

    public function executeDroits(sfWebRequest $request) {
        $this->compte = $this->getUser()->getCompte();
        $this->form = new CompteDroitsForm($this->compte);

        if(!$request->isMethod(sfWebRequest::POST)) {
            return sfView::SUCCESS;
        }

        $this->form->bind($request->getParameter($this->form->getName()));

        if(!$this->form->isValid()) {
            return sfView::SUCCESS;
        }

        $this->form->save();

        return $this->redirect('compte_droits');
    }

    public function executePersonneAjouter(sfWebRequest $request) {
        $this->setTemplate('personne');
        $this->compte = $this->getUser()->getCompte();
        $this->personne = _CompteClient::getInstance()->generateComptePersonne($this->compte);
        
        $this->form = $this->processFormPersonne($this->personne, $request);
    }

    public function executePersonneModifier(sfWebRequest $request) {
        $this->setTemplate('personne');
        $this->compte = $this->getUser()->getCompte();
        $this->personne = _CompteClient::getInstance()->findByLogin($request->getParameter('login'));
        $this->forward404Unless($this->personne);
        
        $this->form = $this->processFormPersonne($this->personne, $request);
    }

    protected function processFormPersonne($personne, sfWebRequest $request) {
        $form = new ComptePersonneForm($personne);

        if(!$request->isMethod(sfWebRequest::POST)) {
            return $form;
        }

        $form->bind($request->getParameter($form->getName()));

        if(!$form->isValid()) {
            return $form;
        }

        $form->save();

        return $this->redirect('compte_droits');
    }
}
