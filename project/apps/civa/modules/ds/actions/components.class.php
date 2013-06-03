<?php

class dsComponents extends sfComponents {
    
    
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
      $this->ds_editable = $this->getUser()->isDsEditable();
    }
    
    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->ds = $this->getUser()->getDs();
        $this->campagnes = $this->getUser()->getTiers('Recoltant')->getDeclarationsArchivesSince(($this->getUser()->getCampagne()-1));
	    $this->has_import =  false;//acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($this->getUser()->getCampagne(), $this->getUser()->getTiers()->cvi);
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
        $this->revendique = array();
        $this->usages_industriels = array();
        $this->libelle = array();
        $this->volume_negoces = array();
        $this->volume_cooperatives = array();
        $this->volume_sur_place = array();
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
              $this->revendique[$appellation->getAppellation()] = $appellation->getVolumeRevendique();
              $this->usages_industriels[$appellation->getAppellation()] = $appellation->getUsagesIndustrielsCalcule();
              $this->volume_sur_place[$appellation->getAppellation()] = $appellation->getTotalCaveParticuliere();
          }
        }
        $this->total_superficie = array_sum(array_values($this->superficie));
        $this->total_volume = array_sum(array_values($this->volume));
        $this->total_usages_industriels= array_sum(array_values($this->usages_industriels));
        $this->total_revendique = array_sum(array_values($this->revendique));
        $this->total_volume_sur_place = array_sum(array_values($this->volume_sur_place));
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
