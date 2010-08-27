<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationActions extends EtapesActions {

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceCiva(sfWebRequest $request) {
        $this->setCurrentEtape('mon_espace_civa');
        $this->campagnes = $this->getUser()->getRecoltant()->getDeclarationArchivesCampagne($this->getUser()->getCampagne());
	krsort($this->campagnes);
        $this->declaration = $this->getUser()->getDeclaration();
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->processChooseDeclaration($request);
        }
    }

    protected function processChooseDeclaration(sfWebRequest $request) {
        $recoltant = $this->getUser()->getRecoltant();
        $dr_data = $this->getRequestParameter('dr', null);
        if ($dr_data) {
            if ($dr_data['type_declaration'] == 'brouillon') {
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'supprimer') {
                $this->getUser()->getDeclaration()->delete();
                $this->redirect('@mon_espace_civa');
            } elseif ($dr_data['type_declaration'] == 'vierge') {
                $doc = new DR();
                $doc->set('_id', 'DR-' . $recoltant->cvi . '-' . $this->getUser()->getCampagne());
                $doc->cvi = $recoltant->cvi;
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->save();
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            } elseif ($dr_data['type_declaration'] == 'precedente') {
                $old_doc = $recoltant->getDeclaration($dr_data['liste_precedentes_declarations']);
		if (!$old_doc) {
		  throw new Exception("Bug: ".$dr_data['liste_precedentes_declarations']." not found :()");
		}
                $doc = clone $old_doc;
                $doc->_id = 'DR-'.$recoltant->cvi.'-'.$this->getUser()->getCampagne();
                $doc->campagne = $this->getUser()->getCampagne();
		$doc->removeVolumes();
		$doc->update();
                $doc->save();
		if ($doc->get('recolte/appellation_KLEVENER/lieu/cepage_KL/detail/0/volume') != 0) {
		  echo "PUTAIN : ".$doc->get('recolte/appellation_KLEVENER/lieu/cepage_KL/detail/0/volume');
		  exit;
		}
                $this->redirectByBoutonsEtapes(array('valider' => 'next'));
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeExploitationAutres(sfWebRequest $request) {
        $this->setCurrentEtape('exploitation_autres');

        $this->form = new ExploitationAutresForm($this->getUser()->getDeclaration());
        
        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));
            if ($this->form->isValid()) {
                $this->form->save();
                if ($this->askRedirectToPreviousEtapes()) {
                    $this->logMessage($this->_etapes_config->previousUrl());
                    $this->redirect($this->_etapes_config->previousUrl().'?from_exploitation_autres=1');
                } else {
                    $this->redirectByBoutonsEtapes();
                }
                
            }
        }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeValidation(sfWebRequest $request) {
      $this->setCurrentEtape('validation');
      $recoltant = $this->getUser()->getRecoltant();
      $annee = $this->getRequestParameter('annee', null);
      $key = 'DR-'.$recoltant->cvi.'-'.$annee;
      $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
      $this->forward404Unless($dr);
      $this->appellations = array();
      $this->superficie = array();
      $this->volume = array();
      $this->revendique = array();
      $this->dplc = array();
      $this->libelle = array();
      $this->volume_negoces = array();
      $this->volume_cooperatives = array();
      $cvi = array();
      $conf = ConfigurationClient::getConfiguration();
      foreach ($dr->recolte as $appellation) {
	$this->appellations[] = $appellation->getAppellation();
	$this->libelle[$appellation->getAppellation()] = $conf->get($appellation->getHash())->getLibelle();
	$this->superficie[$appellation->getAppellation()] = $appellation->getTotalSuperficie();
	$this->volume[$appellation->getAppellation()] = $appellation->getTotalVolume();
	$this->revendique[$appellation->getAppellation()] = $appellation->getTotalVolumeRevendique();
	$this->dplc[$appellation->getAppellation()] = $appellation->getTotalDPLC();
      }

      $this->total_superficie = array_sum(array_values($this->superficie));
      $this->total_volume = array_sum(array_values($this->volume));
      $this->total_dplc = array_sum(array_values($this->dplc));
      $this->total_revendique = array_sum(array_values($this->revendique));

      $this->lies = $dr->lies;
      $this->jeunes_vignes = $dr->jeunes_vignes;

      $this->annee = $annee;

      if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
      }
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeConfirmation(sfWebRequest $request) {
        $this->setCurrentEtape('confirmation');

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->redirectByBoutonsEtapes();
        }
    }

}
