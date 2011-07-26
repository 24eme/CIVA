<?php

/**
 * admin actions.
 *
 * @package    civa
 * @subpackage admin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class adminActions extends sfActions {

    /**
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        $this->getUser()->signOutCompte(myUser::NAMESPACE_COMPTE_PROXY);
        $this->getUser()->signOutCompte(myUser::NAMESPACE_COMPTE_TIERS);
        $this->form = new AdminCompteLoginForm();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->getUser()->signInCompte($this->form->process());
                $this->redirect('@tiers');
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeGamma(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfWebRequest::POST));
        if ($request->getParameter('gamma_type_acces') == 'prod') {
            $this->redirect(sfConfig::get('app_gamma_url_prod'));
        } elseif ($request->getParameter('gamma_type_acces') == 'test') {
            $this->redirect(sfConfig::get('app_gamma_url_qualif'));
        }
    }
}
