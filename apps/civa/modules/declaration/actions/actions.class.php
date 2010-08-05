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
                $this->forward404Unless($old_doc = $recoltant->getDeclaration($dr_data['liste_precedentes_declarations']));
                $doc = clone $old_doc;
                $doc->_id = 'DR-'.$recoltant->cvi.'-'.$this->getUser()->getCampagne();
                $doc->campagne = $this->getUser()->getCampagne();
                $doc->save();
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
                $this->redirectByBoutonsEtapes();
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
	foreach($dr->acheteurs->get('appellation_'.$appellation->getAppellation())->getNegoces() as $n) {
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
      }

      $this->acheteurs = array();
      foreach (array_keys($cvi) as $c) {
	$this->acheteurs[$c] = sfCouchdbManager::getClient()->retrieveDocumentById('ACHAT-'.$c);
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
