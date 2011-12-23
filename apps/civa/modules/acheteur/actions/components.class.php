<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class acheteurComponents extends sfComponents {

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeMonEspace(sfWebRequest $request) {
        $this->csv = sfCouchdbManager::getClient("CSV")->retrieveByCviAndCampagne($this->getUser()->getTiers('Acheteur')->cvi);
        $this->export = sfCouchdbManager::getClient()->retrieveDocumentById("EXPORT-ACHETEURS-".$this->getUser()->getTiers('Acheteur')->cvi);
    }

}
