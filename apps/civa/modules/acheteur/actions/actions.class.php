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

        $this->appellations = ExploitationAcheteursForm::getListeAppellations();

        $this->acheteurs_negociant = include(sfConfig::get('sf_data_dir') . '/acheteurs-negociant.php');
        $this->acheteurs_cave = include(sfConfig::get('sf_data_dir') . '/acheteurs-cave.php');
        $this->acheteurs_mout = array();

        $this->acheteurs_negociant_json = array();
        $this->acheteurs_cave_json = array();
        $this->acheteurs_mout_json = array();
        foreach ($this->acheteurs_negociant as $cvi => $item) {
            $this->acheteurs_mout[$cvi] = $item;
            $this->acheteurs_negociant_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
            $this->acheteurs_mout_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
        }
        foreach ($this->acheteurs_cave as $cvi => $item) {
            $this->acheteurs_mout[$cvi] = $item;
            $this->acheteurs_cave_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
            $this->acheteurs_mout_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
        }

        $this->form = new ExploitationAcheteursForm($declaration->getAcheteurs());

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();

                $declaration->getRecolte()->updateFromAcheteurs();
                $declaration->save();
                
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
	
	try{
	$grdcru = $this->getUser()->getDeclaration()->get('/recolte/appellation_GRDCRU');
	}catch(Exception $e) {
	  return $this->redirect('@exploitation_autres');
	}
	$this->lieux = array();
	foreach ($grdcru->filter('lieu[0-9]') as $key => $lieu) {
	  $this->lieux[$key] = $lieu->getLibelle();
	}

	$this->form = new LieuDitForm();

        if ($request->isMethod(sfWebRequest::POST)) {
	  $this->form->bind($request->getParameter($this->form->getName()));
	  if ($this->form->isValid()) {
	    $grdcru->add($this->form->getValue('lieu'));
	    $grdcru->save();
	    return $this->redirect('@exploitation_lieu');
	  }
	  $this->redirectByBoutonsEtapes();
        }
    }

    public function executeExploitationLieuDelete(sfWebRequest $request) {
      $declaration = $this->getUser()->getDeclaration();
      $declaration->get('/recolte/appellation_GRDCRU')->remove($request->getParameter('lieu'));
      $declaration->save();
      return $this->redirect('@exploitation_lieu');
    }

}
