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
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward('default', 'module');
  }

  /**
   *
   * @param sfWebRequest $request
   */
  public function executeMonEspaceCiva(sfWebRequest $request) {

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
