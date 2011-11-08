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
     $this->forward404Unless($this->tiers = sfCouchdbManager::getClient()->retrieveDocumentById($request->getParameter('id'), sfCouchdbClient::HYDRATE_ARRAY));
     return $this->renderData($this->buildTiersData(array($this->tiers)));
  }

  /*public function executeFindAll(sfWebRequest $request) {
     ini_set('memory_limit', '256M');
     $this->tiers = sfCouchdbManager::getClient("_Tiers")->getAll(sfCouchdbClient::HYDRATE_ARRAY);
     return $this->renderData($this->buildTiersData($this->tiers));
  }

  public function executeFindAllMetteurMarche(sfWebRequest $request) {
     ini_set('memory_limit', '256M');
     $this->tiers = sfCouchdbManager::getClient("MetteurEnMarche")->getAll(sfCouchdbClient::HYDRATE_ARRAY);
     return $this->renderData($this->buildTiersData($this->tiers));
  }
  
  public function executeFindAllRecoltant(sfWebRequest $request) {
     ini_set('memory_limit', '256M');
     $this->tiers = sfCouchdbManager::getClient("Recoltant")->getAll(sfCouchdbClient::HYDRATE_ARRAY);
     return $this->renderData($this->buildTiersData($this->tiers));
  }
  
  public function executeFindAllAcheteur(sfWebRequest $request) {
     ini_set('memory_limit', '256M');
     $this->tiers = sfCouchdbManager::getClient("Acheteur")->getAll(sfCouchdbClient::HYDRATE_ARRAY);
     return $this->renderData($this->buildTiersData($this->tiers));
  }*/

  /*public function executeFindAllDeclarations(sfWebRequest $request) {
    $this->tiers = $this->getTiers();
    $this->declarations = sfCouchdbManager::getClient("DR")->getAllByCvi($this->tiers['cvi'], sfCouchdbClient::HYDRATE_ARRAY);

    return $this->renderData($this->buildDeclarationsData($this->declarations));
  }*/

  /*public function executeFindOneAndAllDeclarations(sfWebRequest $request) {
    $this->tiers = $this->getTiers();
    $this->declarations = sfCouchdbManager::getClient("DR")->getAllByCvi($this->tiers['cvi'], sfCouchdbClient::HYDRATE_ARRAY);

    return $this->renderData(array_merge(
                                $this->buildTiersData(array($this->tiers)),
                                $this->buildDeclarationsData($this->declarations)
                            ));
  }*/

}
