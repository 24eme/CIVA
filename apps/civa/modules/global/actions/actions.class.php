<?php

/**
 * global actions.
 *
 * @package    civa
 * @subpackage global
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class globalActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeMessageAjax(sfWebRequest $request)
  {
      $this->forward404Unless($request->isXmlHttpRequest());
      return $this->renderText(json_encode(array('titre' => $request->getParameter('title', null),
                                                 'message' => sfCouchdbManager::getClient('Messages')->getMessage($request->getParameter('id', null)))));

  }
  public function executeError404(){

  }

  public function executeSecure(){
    if ($this->getUser()->isDeclarant()) {
        return $this->redirect("@mon_espace_civa");
    } elseif ($this->getUser()->isNonDeclarant()) {
        return $this->redirect("@mon_espace_civa_non_declarant");
    } elseif ($this->getUser()->isAdmin()) {
        return $this->redirect("@login_admin");
    }
  }
}
