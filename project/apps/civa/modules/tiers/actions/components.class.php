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

    public function executeMonEspaceColonne(sfWebRequest $request)
    {
        $this->svs = SVClient::getInstance()->getAllByEtablissement($this->etablissement);
        ksort($this->svs);
    }
}
