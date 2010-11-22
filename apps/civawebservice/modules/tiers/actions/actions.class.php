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
     $this->tiers = $this->getTiers();
     return $this->renderData($this->buildTiersData(array($this->tiers)));
  }

  public function executeFindAll(sfWebRequest $request) {
     ini_set('memory_limit', '256M');
     $this->tiers = sfCouchdbManager::getClient("Tiers")->getAll(sfCouchdbClient::HYDRATE_ARRAY);
     return $this->renderData($this->buildTiersData($this->tiers));
  }

  public function executeFindAllDeclarations(sfWebRequest $request) {
    $this->tiers = $this->getTiers();
    $this->declarations = sfCouchdbManager::getClient("DR")->getAllByCvi($this->tiers['cvi'], sfCouchdbClient::HYDRATE_ARRAY);

    return $this->renderData($this->buildDeclarationsData($this->declarations));
  }

  public function executeFindOneAndAllDeclarations(sfWebRequest $request) {
    $this->tiers = $this->getTiers();
    $this->declarations = sfCouchdbManager::getClient("DR")->getAllByCvi($this->tiers['cvi'], sfCouchdbClient::HYDRATE_ARRAY);

    return $this->renderData(array_merge(
                                $this->buildTiersData(array($this->tiers)),
                                $this->buildDeclarationsData($this->declarations)
                            ));
  }

  protected function getTiers($cvi = null) {
      $this->forward404Unless($tiers = sfCouchdbManager::getClient("Tiers")->retrieveByCvi($this->getRequest()->getParameter('cvi'), sfCouchdbClient::HYDRATE_ARRAY));
      return $tiers;
  }
}
