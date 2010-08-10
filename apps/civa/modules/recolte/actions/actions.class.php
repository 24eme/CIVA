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
        $this->list_acheteurs_mout = include(sfConfig::get('sf_data_dir') . '/acheteurs-mout.php');
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecolte(sfWebRequest $request) {
        
        $this->initOnglets($request);
        $this->getDetails();

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->getDetails();
        $this->detail_action_mode = 'update';

        $this->detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($this->detail_key));
        
        $this->form_detail = new RecolteForm($this->details->get($this->detail_key), array('superficie_required' => $this->superficie_required,
                                                                                           'acheteurs_negoce' => $this->acheteurs_negoce,
                                                                                           'acheteurs_cooperative' => $this->acheteurs_cave,
                                                                                           'acheteurs_mout' => $this->acheteurs_mout));

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeAdd(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->getDetails();
        $this->detail_action_mode = 'add';

        $detail = $this->details->add();
        $this->detail_key = $this->details->count() - 1;

        $this->form_detail = new RecolteForm($detail, array('superficie_required' => $this->superficie_required,
                                                            'acheteurs_negoce' => $this->acheteurs_negoce,
                                                            'acheteurs_cooperative' => $this->acheteurs_cave,
                                                            'acheteurs_mout' => $this->acheteurs_mout));

        if ($request->isMethod(sfWebRequest::POST)) {
           $this->processFormDetail($this->form_detail, $request);
        }

        $this->setTemplate('recolte');
    }

    public function executeDelete(sfWebRequest $request) {
        $this->initOnglets($request);
        $this->getDetails();
        
        $detail_key = $request->getParameter('detail_key');
        $this->forward404Unless($this->details->exist($detail_key));

        $this->details->remove($detail_key);
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

        $this->onglets = new RecolteOnglets($this->configuration, $this->declaration);
        if (!$appellation && !$lieu && !$cepage) {
           $this->redirect($this->onglets->getUrl('recolte'));
        }
        $this->forward404Unless($this->onglets->init($appellation, $lieu, $cepage));
	return $this->onglets;
    }
    
    protected function getDetails() {
        $this->details = $this->declaration->get($this->onglets->getItemsCepage()->getHash())
                                     ->add($this->onglets->getCurrentKeyCepage())
                                     ->add('detail');

       $configuration_appellation = $this->configuration->get('recolte')->get($this->onglets->getCurrentKeyAppellation());
       $configuration_lieu = $configuration_appellation->get($this->onglets->getCurrentKeyLieu());
       $configuration_cepage = $configuration_lieu->get($this->onglets->getCurrentKeyCepage());
        
        $this->detail_key = null;
        $this->detail_action_mode = null;
        $this->form_detail = null;

        $this->has_acheteurs_mout = ($configuration_appellation->mout == 1);
        $this->superficie_required = !($configuration_cepage->exist('superficie_optionnelle'));
        $this->acheteurs_negoce = $this->declaration->get('acheteurs')->get($this->onglets->getCurrentKeyAppellation())->get('negoces');
        $this->acheteurs_cave = $this->declaration->get('acheteurs')->get($this->onglets->getCurrentKeyAppellation())->get('cooperatives');
        $this->acheteurs_mout = null;
        if ($this->has_acheteurs_mout) {
            $this->acheteurs_mout = $this->declaration->get('acheteurs')->get($this->onglets->getCurrentKeyAppellation())->get('mouts');
        }
    }


    public function executeRecapitulatif(sfWebRequest $request)
    {
      $this->initOnglets($request);
      
      $dr = $this->getUser()->getDeclaration();
      $appellation = $this->onglets->getItemsLieu();

      $this->volume_negoces = array();
      $this->volume_cooperatives = array();
      $this->volume_mouts = array();

      foreach($dr->acheteurs->get($appellation->getKey())->getNegoces() as $n) {
	$cvi[$n] = 1;
	if (!isset($this->volume_negoces[$n])) {
	  $this->volume_negoces[$n] = new stdClass();
	  $this->volume_negoces[$n]->volume = 0;
	  $this->volume_negoces[$n]->ratio_superficie = 0;
	}
	$vol = $appellation->getVolumeAcheteur($n, 'negoces');
	if ($vol) {
	  $this->volume_negoces[$n]->volume += $vol['volume'];
	  $this->volume_negoces[$n]->ratio_superficie += $vol['ratio_superficie'];
	}
      }
      foreach($dr->acheteurs->get('appellation_'.$appellation->getAppellation())->getCooperatives() as $n) {
	  $cvi[$n] = 1;
	  if (!isset($this->volume_cooperatives[$n])) {
	    $this->volume_cooperatives[$n] = new stdClass();
	    $this->volume_cooperatives[$n]->volume = 0;
	    $this->volume_cooperatives[$n]->ratio_superficie = 0;
	  }
	  $vol = $appellation->getVolumeAcheteur($n, 'cooperatives');
	  if ($vol) {
	    $this->volume_cooperatives[$n]->volume += $vol['volume'];
	    $this->volume_cooperatives[$n]->ratio_superficie += $vol['ratio_superficie'];
	  }
      }

      $this->acheteurs = array();
      foreach (array_keys($cvi) as $c) {
	$this->acheteurs[$c] = sfCouchdbManager::getClient()->retrieveDocumentById('ACHAT-'.$c);
      }
      
    }
}