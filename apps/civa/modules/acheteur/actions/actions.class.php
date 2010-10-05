<?php

/**
 * acheteurs actions.
 *
 * @package    civa
 * @subpackage acheteurs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class acheteurActions extends EtapesActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeExploitationAcheteurs(sfWebRequest $request) {
        $this->setCurrentEtape('exploitation_acheteurs');
        $declaration = $this->getUser()->getDeclaration();

        $this->help_popup_action = "help_popup_exploitation_acheteur";

        $this->appellations = ExploitationAcheteursForm::getListeAppellations();


        $this->acheteurs_negociant_using = $declaration->acheteurs->getArrayNegoces();
        $this->acheteurs_cave_using = $declaration->acheteurs->getArrayCooperatives();
        $this->acheteurs_mout_using = $declaration->acheteurs->getArrayMouts();

        $this->acheteurs_negociant = ListAcheteursConfig::getNegoces();
        $this->acheteurs_cave = ListAcheteursConfig::getCooperatives();
        $this->acheteurs_mout = ListAcheteursConfig::getMouts();

        $this->form = new ExploitationAcheteursForm($declaration->getAcheteurs());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                $this->redirectByBoutonsEtapes();
            }
        }
    }

    public function executeExploitationAcheteursTableRowItemAjax(sfWebRequest $request) {
        if ($request->isXmlHttpRequest() && $request->isMethod(sfWebRequest::POST)) {
            $name = $request->getParameter('qualite_name');
            $donnees = $request->getParameter('donnees');
            $nom = $donnees[0];
            $cvi = $donnees[1];
            $commune = $donnees[2];
            $mout = ($request->getParameter('acheteur_mouts', null) == '1');

            $appellations_form = ExploitationAcheteursForm::getListeAppellations();
            if ($mout) {
                $appellations_form = ExploitationAcheteursForm::getListeAppellationsMout();
            }
            $values = array();
            $i = 3;
            foreach ($appellations_form as $key => $item) {
                $values[$key] = (isset($donnees[$i]) && $donnees[$i] == '1');
                $i++;
            }


            $form = ExploitationAcheteursForm::getNewItemAjax($name, $cvi, $values, $appellations_form);

            return $this->renderPartial('exploitationAcheteursTableRowItem', array('nom' => $nom,
                'cvi' => $cvi,
                'commune' => $commune,
                'appellations' => ExploitationAcheteursForm::getListeAppellations(),
                'form_item' => $form[$name.ExploitationAcheteursForm::FORM_SUFFIX_NEW][$cvi],
                'mout' => $mout));
        } else {
            $this->forward404();
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeExploitationLieu(sfWebRequest $request) {
        $this->setCurrentEtape('exploitation_lieu');
        $this->help_popup_action = "help_popup_exploitation_lieu";
	
	try{
	$grdcru = $this->getUser()->getDeclaration()->get('/recolte/appellation_GRDCRU');
	}catch(Exception $e) {
          if ($this->hasRequestParameter('from_recolte')) {
             return $this->redirectToPreviousEtapes();
          } else {
             return $this->redirectToNextEtapes();
          }
	}
	$this->lieux = array();
	foreach ($grdcru->filter('lieu[0-9]') as $key => $lieu) {
	  $this->lieux[$key] = $lieu->getLibelle();
	}

	$this->form = new LieuDitForm($grdcru, array('lieu_required' => !(count($this->lieux) > 0)));

        if ($request->isMethod(sfWebRequest::POST)) {
	  $this->form->bind($request->getParameter($this->form->getName()));
	  if ($this->form->isValid()) {
	    $this->form->save();
	    return  $this->redirectByBoutonsEtapes();
	  }
        }
    }

    public function executeExploitationLieuDelete(sfWebRequest $request) {
      $declaration = $this->getUser()->getDeclaration();
      $declaration->get('/recolte/appellation_GRDCRU')->remove($request->getParameter('lieu'));
      $declaration->save();
      return $this->redirect('@exploitation_lieu');
    }

}
