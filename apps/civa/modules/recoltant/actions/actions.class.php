<?php

/**
 * recoltant actions.
 *
 * @package    civa
 * @subpackage recoltant
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class recoltantActions extends EtapesActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeLogin(sfWebRequest $request)
  {
     $this->form = new LoginForm();
     print_r(sfConfig::get('acheteurs'));
     if ($request->isMethod(sfWebRequest::POST)) {
         $this->form->bind($request->getParameter($this->form->getName()));
         if ($this->form->isValid()) {
             $this->getUser()->signIn($this->form->getValue('recoltant'));
             $this->redirect('@mon_espace_civa');
         }
     }
  }

  public function executeLogout(sfWebRequest $request)
  {
    $this->getUser()->signOut();
    $this->redirect('@login');
  }
  /**
   *
   * @param sfWebRequest $request 
   */
  public function executeExploitationAdministratif(sfWebRequest $request)
  {
      $this->setCurrentEtape('exploitation_administratif');
      $this->forwardUnless($this->recoltant = $this->getUser()->getRecoltant(), 'declaration', 'monEspaceciva');

      $this->form = new RecoltantExploitantForm($this->getUser()->getRecoltant()->getExploitant());
      $this->form_err = 0;
        
      if ($request->isMethod(sfWebRequest::POST)) {
	$this->form->bind($request->getParameter($this->form->getName()));
	if ($this->form->isValid()) {
	  $this->form->save();
	  $this->redirectByBoutonsEtapes();
	}else
	  $this->form_err = 1;            
      }

      if ($request->isMethod(sfWebRequest::POST)) {
	$this->redirectByBoutonsEtapes();
      }
  }
}
