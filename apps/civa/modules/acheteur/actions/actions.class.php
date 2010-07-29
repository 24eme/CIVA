<?php

/**
 * acheteurs actions.
 *
 * @package    civa
 * @subpackage acheteurs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class acheteurActions extends EtapesActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeExploitationAcheteurs(sfWebRequest $request)
  {
        $this->setCurrentEtape('exploitation_acheteurs');
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
  }
}
