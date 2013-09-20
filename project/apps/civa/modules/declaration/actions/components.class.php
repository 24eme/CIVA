<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class declarationComponents extends sfComponents {
    
    
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
      $this->dr_editable = $this->getUser()->isDrEditable();
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->declaration = $this->getUser()->getDeclaration();
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDeclarationsArchivesSince(($this->getUser()->getCampagne()-1));
	    $this->has_import =  acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($this->getUser()->getCampagne(), $this->getUser()->getTiers()->cvi);
        krsort($this->campagnes);
    }
    
    /**
     *
     * @param sfWebRequest $request 
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDeclarationsArchivesSince(($this->getUser()->getCampagne()-1));
        krsort($this->campagnes);
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeRecapDeclaration(sfWebRequest $request) {
        $this->appellations = array();
        $this->superficie = array();
        $this->volume = array();
        $this->volume_vendus = array();
        $this->revendique = array();
        $this->revendique_sur_place = array();
        $this->usages_industriels = array();
        $this->usages_industriels_sur_place = array();
        $this->libelle = array();
        $this->volume_negoces = array();
        $this->volume_cooperatives = array();
        $this->volume_sur_place = array();
        $this->volume_rebeches = array();
        $this->has_no_usages_industriels = $this->dr->recolte->getConfig()->hasNoUsagesIndustriels();
        $cvi = array();
        foreach ($this->dr->recolte->getNoeudAppellations()->getConfig()->filter('^appellation_') as $appellation_key => $appellation_config) {
          if ($this->dr->recolte->getNoeudAppellations()->exist($appellation_key)) {
              $appellation = $this->dr->recolte->getNoeudAppellations()->get($appellation_key);
              if ($appellation->getConfig()->excludeTotal())
                continue;
              $this->appellations[] = $appellation->getAppellation();
              $this->libelle[$appellation->getAppellation()] = $appellation->getConfig()->getLibelle();
              $this->superficie[$appellation->getAppellation()] = $appellation->getTotalSuperficie();
              $this->volume[$appellation->getAppellation()] = $appellation->getTotalVolume();
              $this->volume_vendus[$appellation->getAppellation()] = $appellation->getTotalVolumeVendus();
              $this->revendique[$appellation->getAppellation()] = $appellation->getVolumeRevendique();
              if($this->dr->recolte->getTotalCaveParticuliere() > 0) {
                $this->revendique_sur_place[$appellation->getAppellation()] = $appellation->getVolumeRevendiqueCaveParticuliere();
              }
              $this->usages_industriels[$appellation->getAppellation()] = $appellation->getUsagesIndustriels();
              if($this->dr->recolte->getTotalCaveParticuliere() > 0) {
                $this->usages_industriels_sur_place[$appellation->getAppellation()] = $appellation->getUsagesIndustrielsCaveParticuliere();
              }
              $this->volume_sur_place[$appellation->getAppellation()] = $appellation->getTotalCaveParticuliere();
              if($appellation->hasCepageRB()) {
                $this->volume_rebeches[$appellation->getAppellation()] = $appellation->getTotalRebeches();
              }
          }
        }
        $this->total_superficie = array_sum(array_values($this->superficie));
        $this->total_volume = array_sum(array_values($this->volume));
        $this->total_volume_vendus = array_sum(array_values($this->volume_vendus));
        $this->total_usages_industriels= array_sum(array_values($this->usages_industriels));
        if(count($this->usages_industriels_sur_place) > 0) {
          $this->total_usages_industriels_sur_place= array_sum(array_values($this->usages_industriels_sur_place));
        }
        $this->total_revendique = array_sum(array_values($this->revendique));
        if(count($this->revendique_sur_place) > 0) {
          $this->total_revendique_sur_place = array_sum(array_values($this->revendique_sur_place));
        }
        $this->total_volume_sur_place = array_sum(array_values($this->volume_sur_place));
        if(count($this->volume_rebeches) > 0) {
          $this->total_volume_rebeches = array_sum(array_values($this->volume_rebeches));
        }
        $this->lies = $this->dr->lies;
        $this->jeunes_vignes = $this->dr->jeunes_vignes;
	
        $this->vintable = array();
        if ($this->dr->recolte->certification->genre->exist('appellation_VINTABLE')) {
          $this->vintable['superficie'] = $this->dr->recolte->certification->genre->appellation_VINTABLE->getTotalSuperficie();
          $this->vintable['volume'] = $this->dr->recolte->certification->genre->appellation_VINTABLE->getTotalVolume();
        }
    $this->annee = $this->dr->campagne;
  }
}
