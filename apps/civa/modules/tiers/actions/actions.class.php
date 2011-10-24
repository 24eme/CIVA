<?php

/**
 * tiers actions.
 *
 * @package    civa
 * @subpackage tiers
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tiersActions extends EtapesActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeLogin(sfWebRequest $request) {
        $this->getUser()->signOutTiers();
        $this->compte = $this->getUser()->getCompte();
	
	$not_uniq = 0;
	$tiers = array();
        if (count($this->compte->tiers) >= 1) {
	  foreach ($this->compte->tiers as $t) {
	    if (isset($tiers[$t->type])) {
	      $not_uniq = 1;
	      continue;
	    }
	    $tiers[$t->type] = sfCouchdbManager::getClient()->retrieveDocumentById($t->id);
	  }
	  if (!$not_uniq) {
	    $this->getUser()->signInTiers(array_values($tiers));
	    return $this->redirect("@mon_espace_civa");
	  }
        }

        $this->form = new TiersLoginForm($this->compte);

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $t = $this->form->process();
		$tiers[$t->type] = $t;
                $this->getUser()->signInTiers(array_values($tiers));
                $this->redirect("@mon_espace_civa");
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->help_popup_action = "help_popup_mon_espace_civa";
        $this->setCurrentEtape('mon_espace_civa');
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeExploitationAdministratif(sfWebRequest $request) {
        $this->setCurrentEtape('exploitation_administratif');
        $this->help_popup_action = "help_popup_exploitation_administratif";

        $this->forwardUnless($this->tiers = $this->getUser()->getTiers(), 'declaration', 'monEspaceciva');

        $this->form_gest = new TiersExploitantForm($this->getUser()->getTiers()->getExploitant());
        $this->form_gest_err = 0;
        $this->form_expl = new TiersExploitationForm($this->getUser()->getTiers());
        $this->form_expl_err = 0;

        if ($request->isMethod(sfWebRequest::POST)) {
            if ($request->getParameter('gestionnaire')) {
                $this->form_gest->bind($request->getParameter($this->form_gest->getName()));
                if ($this->form_gest->isValid()) {
                    $this->form_gest->save();
                } else {
                    $this->form_gest_err = 1;
                }
            }
            if ($request->getParameter('exploitation')) {
                $this->form_expl->bind($request->getParameter($this->form_expl->getName()));
                if ($this->form_expl->isValid()) {

                    $tiers = $this->form_expl->save();
                    // $ldap = new ldap();

                    if ($tiers) {
                        /* $values['nom'] = $tiers->nom;
                          $values['adresse'] = $tiers->siege->adresse;
                          $values['code_postal'] = $tiers->siege->code_postal;
                          $values['ville'] = $tiers->siege->commune; */
                        //$ldap->ldapModify($this->getUser()->getTiers());
                    }
                } else {
                    $this->form_expl_err = 1;
                }
            }
            if (!$this->form_gest_err && !$this->form_expl_err) {
                $dr = $this->getUser()->getDeclaration();
                $dr->declarant->nom = $this->tiers->exploitant->nom;
                $dr->declarant->telephone = $this->tiers->exploitant->telephone;
                $dr->declarant->email = $this->tiers->email;
                $dr->save();
                $boutons = $this->getRequestParameter('boutons', null);
            	if ($boutons && in_array('previous', array_keys($boutons))) {
		            $this->redirect('@mon_espace_civa');
		    	} else {
                	$this->redirectByBoutonsEtapes();
		    	}
            }
        }
    }

}
