<?php

/**
 * compte actions.
 *
 * @package    civa
 * @subpackage compte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class compteActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {

       
        $this->form = new FirstConnectionForm();
        $this->erreur = '';
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter('firstConnection'));

            if ($this->form->isValid()) {
                $this->redirect('compte/create');
            }
        }

    }
   
    public function executeCreate(sfWebRequest $request) {
        $this->form = new CreateCompteForm();
    }


}
