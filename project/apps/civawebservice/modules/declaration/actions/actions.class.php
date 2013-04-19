<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends DataManipulationActions
{
    /* public function executeFindAll(sfWebRequest $request) {
         ini_set('memory_limit', '256M');
         set_time_limit('180');
         $ids = acCouchdbManager::getClient("DR")->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
         $this->declarations = array();
         foreach($ids as $id) {
             $this->declarations[$id] = $this->buildItemDeclarations(acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_ARRAY));
         }
         return $this->renderData($this->buildDeclarationsData($this->declarations));
    }

    public function executeFindAllByCampagne(sfWebRequest $request) {
        ini_set('memory_limit', '256M');
         $this->forward404Unless($campagne = $request->getParameter('campagne'));
         $ids = acCouchdbManager::getClient("DR")->getAllByCampagne($campagne, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
         $this->declarations = array();
         foreach($ids as $id) {
             $this->declarations[$id] = $this->buildItemDeclarations(acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_ARRAY));
         }
         return $this->renderData($this->buildDeclarationsData($this->declarations));
    }*/
}
