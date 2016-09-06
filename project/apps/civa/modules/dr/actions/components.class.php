<?php

/**
 * declaration actions.
 *
 * @package    civa
 * @subpackage declaration
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class drComponents extends sfComponents {

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspace(sfWebRequest $request) {
      $this->dr_editable = $this->getUser()->isDrEditable();
      $this->dr = $this->getUser()->getDeclaration();
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->declaration = $this->getUser()->getDeclaration();
        $this->etablissement = $this->getUser()->getTiers('Recoltant');
        $this->campagne = $this->getUser()->getCampagne();
        $this->campagnes = acCouchdbManager::getClient('DR')->getArchivesSince($this->getUser()->getTiers('Recoltant')->cvi, ($this->getUser()->getCampagne()-1), 4);
	      $this->has_import =  acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($this->getUser()->getCampagne(), $this->getUser()->getTiers()->cvi);
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->campagnes = acCouchdbManager::getClient('DR')->getArchivesSince($this->getUser()->getTiers('Recoltant')->cvi, ($this->getUser()->getCampagne()-1), 4);;
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
        $this->volume_rebeches_sur_place = array();
        $this->has_no_usages_industriels = $this->dr->recolte->getConfig()->hasNoUsagesIndustriels();
        $this->has_no_recapitulatif_couleur = $this->dr->recolte->getConfig()->hasNoRecapitulatifCouleur();
        $this->can_calcul_volume_revendique_sur_place = $this->dr->recolte->canCalculVolumeRevendiqueSurPlace();
        $cvi = array();
        foreach ($this->dr->recolte->getNoeudAppellations()->getConfig()->getAppellations() as $appellation_key => $appellation_config) {
            if ($this->dr->recolte->getNoeudAppellations()->exist("appellation_".$appellation_key)) {
                $appellation = $this->dr->recolte->getNoeudAppellations()->get("appellation_".$appellation_key);
                if ($appellation->getConfig()->excludeTotal()) {
                    continue;
                }
                foreach($appellation->getMentions() as $mention) {
                    $this->appellations[] = $mention->getHash();
                    $this->libelle[$mention->getHash()] = $appellation->getConfig()->getLibelle()." ".$mention->getConfig()->getLibelle();
                    $this->superficie[$mention->getHash()] = $mention->getTotalSuperficie();
                    $this->volume[$mention->getHash()] = $mention->getTotalVolume();
                    $this->volume_vendus[$mention->getHash()] = $mention->getTotalVolumeVendus();
                    $this->revendique[$mention->getHash()] = $mention->getVolumeRevendique();
                    $this->revendique_sur_place[$mention->getHash()] = $mention->getVolumeRevendiqueCaveParticuliere();
                    $this->usages_industriels[$mention->getHash()] = $mention->getUsagesIndustriels();
                    $this->usages_industriels_sur_place[$mention->getHash()] = $mention->getUsagesIndustrielsSurPlace();
                    $this->volume_sur_place[$mention->getHash()] = $mention->getTotalCaveParticuliere();
                    if($mention->getConfig()->hasCepageRB()) {
                        $this->volume_rebeches[$mention->getHash()] = $mention->getTotalRebeches();
                        $this->volume_rebeches_sur_place[$mention->getHash()] = $mention->getSurPlaceRebeches();
                    }
                }
            }
        }
        $this->total_superficie = array_sum(array_values($this->superficie));
        $this->total_volume = array_sum(array_values($this->volume));
        if($this->dr->recolte->getTotalVolumeVendus() > 0 && !$this->has_no_usages_industriels && !$this->has_no_recapitulatif_couleur) {
          $this->total_volume_vendus = array_sum(array_values($this->volume_vendus));
        }
        $this->total_usages_industriels= array_sum(array_values($this->usages_industriels));
        if($this->dr->recolte->getTotalVolumeVendus() > 0 && $this->can_calcul_volume_revendique_sur_place && !$this->has_no_usages_industriels && !$this->has_no_recapitulatif_couleur) {
          $this->total_usages_industriels_sur_place= array_sum(array_values($this->usages_industriels_sur_place));
        }
        $this->total_revendique = array_sum(array_values($this->revendique));
        if($this->dr->recolte->getTotalVolumeVendus() > 0 && $this->can_calcul_volume_revendique_sur_place && !$this->has_no_usages_industriels && !$this->has_no_recapitulatif_couleur) {
          $this->total_revendique_sur_place = array_sum(array_values($this->revendique_sur_place));
        }
        $this->total_volume_sur_place = array_sum(array_values($this->volume_sur_place));
        if(count($this->volume_rebeches) > 0 && !$this->has_no_usages_industriels && !$this->has_no_recapitulatif_couleur) {
          $this->total_volume_rebeches = array_sum(array_values($this->volume_rebeches));
          $this->total_volume_rebeches_sur_place = array_sum(array_values($this->volume_rebeches_sur_place));
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
