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
        $this->form = new DelegationTiersLoginForm($this->getUser()->getCompte());
    }
}
