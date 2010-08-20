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
        $recoltant = $this->getUser()->getRecoltant();
        if(isset($recoltant) && $recoltant->change_mdp == 1) {
            $this->redirect('@mon_espace_civa');
        }else {
            $this->form = new FirstConnectionForm();
            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    if(!$recoltant) $this->getUser()->signIn($this->form->getValue('recoltant'));
                    $this->redirect('compte/create');
                }
            }
        }

    }

    public function executeCreate(sfWebRequest $request) {
        $recoltant = $this->getUser()->getRecoltant();
        if(isset($recoltant) && $recoltant->change_mdp == 1) {
            $this->redirect('@mon_espace_civa');
        }elseif($recoltant) {

            $this->form = new CreateCompteForm();

            if ($request->isMethod(sfWebRequest::POST)) {
                $this->form->bind($request->getParameter($this->form->getName()));

                if ($this->form->isValid()) {
                    $this->addToLdap($recoltant);
                    $this->redirect('@mon_espace_civa');
                }
            }
        }else {
            $this->redirect('compte');
        }
    }

    private function addToLdap($recoltant) {
        $ldap = new ldap();
        $ldapAdd = $ldap->ldapAdd($recoltant);
        print_r($ldapAdd);
        exit();
    }

    public function executeResetMDP(sfWebRequest $request) {
        $recoltant = $this->getUser()->getRecoltant();
        $recoltant->mdp = md5('0000');
        $recoltant->change_mdp = 0;
        $recoltant->save();
        exit();
    }


}
