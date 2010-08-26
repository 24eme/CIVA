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
        
        if(isset($recoltant) && substr($recoltant->mot_de_passe,0,6) == '{SSHA}') {
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
        if(isset($recoltant) && substr($recoltant->mot_de_passe,0,6) == '{SSHA}') {
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
    }

    public function executeResetMDP(sfWebRequest $request) {
        $recoltant = sfCouchdbManager::getClient('Recoltant')->retrieveByCvi('6823700100');
        $recoltant->mot_de_passe = '{TEXT}0000';
        $recoltant->save();
        $ldap = new ldap();
        $ldapDelete = $ldap->ldapDelete($recoltant);
        echo "recoltant remis a 0 - effacer du LDAP";
        exit();
    }



}
