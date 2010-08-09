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
        $this->configuration = ConfigurationClient::getConfiguration();
        $this->declaration = $this->getUser()->getDeclaration();
        $this->list_acheteurs_negoce = include(sfConfig::get('sf_data_dir') . '/acheteurs-negociant.php');
        $this->list_acheteurs_cave = include(sfConfig::get('sf_data_dir') . '/acheteurs-cave.php');
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        
        $this->initOnglets();
        $this->getDetails();

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->initOnglets();
        $this->getDetails();
        $this->detail_action_mode = 'update';

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));
        
        $this->form_detail = new RecolteForm($this->details->get($this->detail_key), array('acheteurs_negoce' => $this->acheteurs_negoce, 'acheteurs_cooperative' => $this->acheteurs_cave));

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeAdd(sfWebRequest $request) {
        $this->initOnglets();
        $this->getDetails();
        $this->detail_action_mode = 'add';

        $detail = $this->details->add();
        $this->detail_key = $this->details->count() - 1;

        $this->form_detail = new RecolteForm($detail, array('acheteurs_negoce' => $this->acheteurs_negoce, 'acheteurs_cooperative' => $this->acheteurs_cave));

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeDelete(sfWebRequest $request) {
        $this->initOnglets();
        $this->getDetails();
        
        $detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($detail_key));

        $this->details->remove($detail_remove_key);
        $this->declaration->save();
        
        $this->redirect($this->onglets->getUrl('recolte'));
    }

    protected function processFormDetail($form, $request) {
        $form->bind($request->getParameter($form->getName()));
        if ($form->isValid()) {
            $form->save();
            $this->redirect($this->onglets->getUrl('recolte'));
        }
    }

    protected function initOnglets() {
        preg_match('/(?P<appellation>\w+)-(?P<lieu>\w*)/',$this->getRequestParameter('appellation_lieu', null), $appellation_lieu);
        $appellation = null;
        if (isset($appellation_lieu['appellation'])) {
            $appellation = $appellation_lieu['appellation'];
        }
        $lieu = null;
        if (isset($appellation_lieu['lieu'])) {
            $lieu = $appellation_lieu['lieu'];
        }
        $cepage = $this->getRequestParameter('cepage', null);

        $this->onglets = new RecolteOnglets($this->configuration, $this->declaration);
        if (!$appellation && !$lieu && !$cepage) {
           $this->redirect($this->onglets->getUrl('recolte'));
        }
        $this->forward404Unless($this->onglets->init($appellation, $lieu, $cepage));
    }
    
    protected function getDetails() {
        $this->details = $this->declaration->get($this->onglets->getItemsCepage()->getHash())
                                     ->add($this->onglets->getCurrentKeyCepage())
                                     ->add('detail');
        $this->detail_key = null;
        $this->detail_action_mode = null;
        $this->form_detail = null;

        $this->acheteurs_negoce = $this->declaration->get('Acheteurs')->get($this->onglets->getCurrentKeyAppellation())->get('negoces');
        $this->acheteurs_cave = $this->declaration->get('Acheteurs')->get($this->onglets->getCurrentKeyAppellation())->get('cooperatives');
    }
}