<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tiersComponents extends sfComponents {

    public function executeDelegationForm()
    {
        if (!$this->form) {
            $this->form = new DelegationLoginForm($this->getUser()->getCompte());
        }
    }

    protected function prepareOnglets()
    {
        if (!isset($this->blocs) || !$this->blocs) {
            $this->blocs = myUser::buildBlocs($this, $this->compte, $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN));
        }
        if(!isset($this->isAdmin)) {
            $this->isAdmin = $this->getUser()->hasCredential(myUser::CREDENTIAL_ADMIN);
        }
    }

    public function executeOnglets()
    {
        $this->prepareOnglets();
    }

    public function executeOngletsBootstrap()
    {
        $this->prepareOnglets();
    }

}
