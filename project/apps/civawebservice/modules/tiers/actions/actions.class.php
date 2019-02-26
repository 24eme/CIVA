<?php

/**
 * tiers actions.
 *
 * @package    civa
 * @subpackage tiers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tiersActions extends DataManipulationActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeFindOne(sfWebRequest $request)
  {
     $this->forward404Unless(preg_match('/^(REC|MET|ACHAT)/', $request->getParameter('id')));
     $this->forward404Unless($this->tiers = acCouchdbManager::getClient()->find($request->getParameter('id'), acCouchdbClient::HYDRATE_ARRAY));
     return $this->renderData($this->buildTiersData(array($this->tiers)));
  }

}
