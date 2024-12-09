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

        $url = sfConfig::get('app_cas_url') . '/login?service=' . str_replace('http://', 'https://', urlencode(preg_replace("/\?$/", '', $request->getUri())));

        return $this->redirect($url);
    }

    protected function redirectAfterLogin($request) {
        if($request->getParameter('ticket')) {
            $this->getUser()->setFlash('referer', preg_replace("/\?$/", "", preg_replace("/ticket=[a-zA-Z0-9-]+(&|$)/", "", $request->getUri())));
        }

        if($this->getUser()->isAdmin() || $this->getUser()->isSimpleOperateur()) {
            return $this->redirect('admin');
        }


        if(!count($this->getUser()->getCompte()->getSociete()->etablissements)) {
            return $this->redirect('compte_modification', ['identifiant' => $this->getUser()->getCompte()->login]);
        }

        return $this->redirect('tiers');
    }

    public function executeLoginNoCas(sfWebRequest $request) {
        if (!(sfConfig::has('app_login_no_cas') && sfConfig::get('app_login_no_cas'))) {

            return $this->forward404Unless();
        }

        if($this->getUser()->hasCredential(myUser::CREDENTIAL_COMPTE)) {

            return $this->redirectAfterLogin($request);
        }

        $this->getUser()->signOut();

        $this->form = new AdminCompteLoginForm();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signIn($this->form->process()->login);
                $this->redirectAfterLogin($request);
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

        $url = 'http'.($request->isSecure() ? 's': null).'://'.$request->getHost();
        error_reporting(E_ALL);
        phpCAS::client(CAS_VERSION_2_0,sfConfig::get('app_cas_domain'), sfConfig::get('app_cas_port'), sfConfig::get('app_cas_path'), false);

        if (sfConfig::get('app_giilda_url_logout')) {

            $url = sfConfig::get('app_giilda_url_logout')."?url=".$url;
        }

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
        if(!sfConfig::get('app_giilda_url_code_creation')) {

            throw new Exception('app paramètre non défini : app_giilda_url_code_creation');
        }

        return $this->redirect(sfConfig::get('app_giilda_url_code_creation'));
    }

    public function executeModification(sfWebRequest $request) {
        if(!sfConfig::get('app_giilda_url_mon_compte')) {

            throw new Exception('app paramètre non défini : app_giilda_url_mon_compte');
        }

        return $this->redirect(sprintf(sfConfig::get('app_giilda_url_mon_compte'), $request->getParameter('identifiant') ? $request->getParameter('identifiant') : $this->getUser()->getCompte()->login));
    }

    public function executeMotDePasseOublieLogin(sfWebRequest $request) {
        if(!sfConfig::get('app_giilda_url_mot_de_passe_oublie_login')) {

            throw new Exception('app paramètre non défini : app_giilda_url_mot_de_passe_oublie_login');
        }

        return $this->redirect(sprintf(sfConfig::get('app_giilda_url_mot_de_passe_oublie_login'), $request->getParameter('login'), $request->getParameter('mdp')));
    }

    public function executeMotDePasseOublie(sfWebRequest $request) {
        if(!sfConfig::get('app_giilda_url_mot_de_passe_oublie')) {

            throw new Exception('app paramètre non défini : app_giilda_url_mot_de_passe_oublie');
        }

        return $this->redirect(sfConfig::get('app_giilda_url_mot_de_passe_oublie'));
    }

}
