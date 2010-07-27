<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends sfActions
{
 
  /**
   *
   * @param sfWebRequest $request
   */
  public function executeMonEspaceCiva(sfWebRequest $request) {
        $campagne = '2010';
        $documents = new sfCouchdbDocumentCollection(sfCouchdbManager::getClient()->startkey('DR-6700800820-0000')->endkey('DR-6700800820-9999')->getAllDocs());

        foreach($documents as $doc) {
            print_r($doc->campagne);
        }
  }

  /**
   *
   * @param sfWebRequest $request 
   */
  public function executeExploitationAutres(sfWebRequest $request) {
      
  }
  
  /**
   *
   * @param sfWebRequest $request
   */
  public function executeRecolte(sfWebRequest $request) {

  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeValidation(sfWebRequest $request) {

  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeConfirmation(sfWebRequest $request) {

  }

  
}
