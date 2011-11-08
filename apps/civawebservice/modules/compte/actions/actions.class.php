<?php

/**
 * compte actions.
 *
 * @package    civa
 * @subpackage compte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class compteActions extends DataManipulationActions
{
    
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeFindOne(sfWebRequest $request)
  {
     $this->forward404Unless($compte_object = sfCouchdbManager::getClient("_Compte")->retrieveByLogin($request->getParameter('login')));
     $this->compte = $compte_object->toArray(2);
     $this->compte['nom'] = $compte_object->getNom();
     
     return $this->renderData($this->buildCompteData(array($this->compte)));
  }
  
  public function executeFindAllDeclarations(sfWebRequest $request) {
    $this->forward404Unless($compte = sfCouchdbManager::getClient("_Compte")->retrieveByLogin($request->getParameter('login'), sfCouchdbClient::HYDRATE_JSON));
    $cvi = null;
    foreach($compte->tiers as $tiers) {
        if ($tiers->type == 'Recoltant') {
            $cvi = str_replace("REC-", "", $tiers->id);
        }
    }
    $this->declarations = sfCouchdbManager::getClient("DR")->getAllByCvi($cvi, sfCouchdbClient::HYDRATE_ARRAY);
    return $this->renderData($this->buildDeclarationsData($this->declarations));
  }
  
}
