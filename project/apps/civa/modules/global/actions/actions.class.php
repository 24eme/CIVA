<?php

/**
 * global actions.
 *
 * @package    civa
 * @subpackage global
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class globalActions extends sfActions {
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeMessageAjax(sfWebRequest $request) {
        $this->forward404Unless($request->isXmlHttpRequest());
        return $this->renderText(json_encode(array('titre' => $request->getParameter('title', null),
						   'url_doc' => $request->getParameter('url_doc', $this->generateUrl('dr_telecharger_la_notice')),
                'message' => acCouchdbManager::getClient('Messages')->getMessage($request->getParameter('id', null)))));

    }
    public function executeError404() {

    }

    public function executeSecure() {
        if (!$this->getUser()->hasCredential(myUser::CREDENTIAL_TIERS) && $this->getUser()->hasCredential(myUser::CREDENTIAL_OPERATEUR)) {
            return $this->redirect("admin");
        } elseif (!$this->getUser()->hasCredential(myUser::CREDENTIAL_TIERS) && $this->getUser()->hasCredential(myUser::CREDENTIAL_COMPTE_TIERS)) {
            return $this->redirect("tiers");
        } elseif($this->getUser()->hasCredential(myUser::CREDENTIAL_COMPTE) && !$this->getUser()->hasCredential(myUser::CREDENTIAL_TIERS)) {
            return $this->redirect("compte_modification");
        } elseif(!$this->getUser()->hasCredential(myUser::CREDENTIAL_COMPTE)) {
            $this->getUser()->signOut();
            return $this->redirect("login");
        } else {
            return $this->redirect("mon_espace_civa", array('identifiant' => $this->getUser()->getCompte()->getIdentifiant()));
        }
    }
}
