<?php

/**
 * recolte actions.
 *
 * @package    civa
 * @subpackage recolte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class recolteActions extends EtapesActions {

    public function preExecute() {
        $this->setCurrentEtape('recolte');
        $this->declaration = $this->getUser()->getDeclaration();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();
        
        $this->detail_action_mode = 'update';

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));
        
        $this->form_detail = new RecolteForm($this->details->get($this->detail_key), $this->getFormDetailsOptions());

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeAdd(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        $this->initAcheteurs();
        
        $this->detail_action_mode = 'add';

        $detail = $this->details->add();
        $this->detail_key = $this->details->count() - 1;

        $this->form_detail = new RecolteForm($detail, $this->getFormDetailsOptions());

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeDelete(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->initDetails();
        
        $detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($detail_key));

        $this->details->remove($detail_key);
        $this->declaration->save();
        
        $this->redirect($this->onglets->getUrl('recolte'));
    }

    public function executeRecapitulatif(sfWebRequest $request)
    {
      $this->initOnglets($request);
      $dr = $this->getUser()->getDeclaration();
      $this->appellationlieu = $this->onglets->getLieu();

      $this->form = new RecapitulatifForm($this->appellationlieu);

      $forms = $this->form->getEmbeddedForms();
      if (!count($forms) && $request->getParameter('redirect')) {
	return $this->redirect($this->onglets->getNextUrl()); 
      }

      if ($request->isMethod(sfWebRequest::POST)) {
	$this->form->bind($request->getParameter($this->form->getName()));
	if ($this->form->isValid()) {
	  $this->form->save();
	  return $this->redirect($this->onglets->getNextUrl());
	}
      }
      

      return;
    }

    protected function processFormDetail($form, $request) {
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
            $form->save();
            $this->redirect($this->onglets->getUrl('recolte'));
        }
    }

    protected function initOnglets(sfWebRequest $request) {
        preg_match('/(?P<appellation>\w+)-?(?P<lieu>\w*)/', $request->getParameter('appellation_lieu', null), $appellation_lieu);
        $appellation = null;
        if (isset($appellation_lieu['appellation'])) {
            $appellation = $appellation_lieu['appellation'];
        }
        $lieu = null;
        if (isset($appellation_lieu['lieu'])) {
            $lieu = $appellation_lieu['lieu'];
        }
        $cepage = $request->getParameter('cepage', null);

        $this->onglets = new RecolteOnglets($this->declaration);
        if (!$appellation && !$lieu && !$cepage) {
           $this->redirect($this->onglets->getUrl('recolte'));
        }
        $this->forward404Unless($this->onglets->init($appellation, $lieu, $cepage));
	return $this->onglets;
    }

    protected function initDetails() {
        $this->details = $this->onglets->getCurrentLieu()->add($this->onglets->getCurrentKeyCepage())->add('detail');

        $this->detail_key = null;
        $this->detail_action_mode = null;
        $this->form_detail = null;
    }

    protected function initAcheteurs() {
        $this->has_acheteurs_mout = ($this->onglets->getCurrentAppellation()->getConfig()->mout == 1);
        $this->acheteurs = $this->declaration->get('acheteurs')->get($this->onglets->getCurrentKeyAppellation());
    }

     protected function getFormDetailsOptions() {
         return array('superficie_required' => (!($this->onglets->getCurrentCepage()->getConfig()->exist('superficie_optionnelle'))),
                      'acheteurs_negoce' => $this->acheteurs->negoces,
                      'acheteurs_cooperative' => $this->acheteurs->cooperatives,
                      'acheteurs_mout' => $this->acheteurs->mouts);
     }
}