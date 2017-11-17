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
    public function executeMonEspaceEnCours(sfWebRequest $request) {
        $this->campagnes = DRClient::getInstance()->getArchivesSince($this->etablissement->getIdentifiant(), ($this->campagne-1), 4);
	      $this->has_import = acCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($this->campagne, $this->etablissement->getIdentifiant());
    }

    /**
     *
     * @param sfWebRequest $request
     */
    public function executeMonEspaceColonne(sfWebRequest $request) {
        $this->campagnes = acCouchdbManager::getClient('DR')->getArchivesSince($this->etablissement->getIdentifiant(), ($this->getUser()->getCampagne()-1), 4);;
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
        if($this->dr->recolte->canHaveVci()) {
            $this->volume_vci = array();
            $this->volume_vci_sur_place = array();
        }
        $this->has_no_usages_industriels = $this->dr->recolte->getConfig()->hasNoUsagesIndustriels();
        $this->has_no_recapitulatif_couleur = $this->dr->recolte->getConfig()->hasNoRecapitulatifCouleur();
        $this->can_calcul_volume_revendique_sur_place = $this->dr->recolte->canCalculVolumeRevendiqueSurPlace();

        $cvi = array();

        foreach ($this->dr->getAppellationsAvecVtsgn() as $key => $appellation) {
            //$appellation = $this->dr->recolte->getNoeudAppellations()->get("appellation_".$appellation_key);

            /*foreach($appellation->getMentions() as $mention) {*/
                $this->appellations[$key] = $key;
                $this->superficie[$key] = 0;
                $this->volume[$key] = 0;
                $this->volume_vendus[$key] = 0;
                $this->revendique[$key] = 0;
                $this->revendique_sur_place[$key] = 0;
                $this->usages_industriels[$key] = 0;
                $this->usages_industriels_sur_place[$key] = 0;
                $this->volume_sur_place[$key] = 0;
                if(isset($this->volume_vci)) {
                    $this->volume_vci[$key] = 0;
                }
                if(isset($this->volume_vci_sur_place)) {
                    $this->volume_vci_sur_place[$key] = 0;
                }
                $this->libelle[$key] = $appellation['libelle'];
                $exclude = false;
                foreach($appellation['noeuds'] as $noeud) {
                    if ($noeud->getAppellation()->getConfig()->excludeTotal()) {
                        $exclude = true;
                        continue;
                    }
                    $this->superficie[$key] += $noeud->getTotalSuperficie();
                    $this->volume[$key] += $noeud->getTotalVolume();
                    $this->volume_vendus[$key] += $noeud->getTotalVolumeVendus();
                    $this->revendique[$key] += $noeud->getVolumeRevendique();
                    $this->revendique_sur_place[$key] += $noeud->getVolumeRevendiqueCaveParticuliere();
                    $this->usages_industriels[$key] += $noeud->getUsagesIndustriels();
                    $this->usages_industriels_sur_place[$key] += $noeud->getUsagesIndustrielsSurPlace();
                    $this->volume_sur_place[$key] += $noeud->getTotalCaveParticuliere();
                    if(isset($this->volume_vci)) {
                        $this->volume_vci[$key] += $noeud->getTotalVci();
                    }
                    if(isset($this->volume_vci_sur_place)) {
                        $this->volume_vci_sur_place[$key] += $noeud->getVciCaveParticuliere();
                    }
                    if($noeud->getConfig()->hasCepageRB()) {
                        if(!isset($this->volume_rebeches[$key])) {
                            $this->volume_rebeches[$key] = 0;
                        }
                        if(!isset($this->volume_rebeches_sur_place[$key])) {
                            $this->volume_rebeches_sur_place[$key] = 0;
                        }
                        $this->volume_rebeches[$key] += $noeud->getTotalRebeches();
                        $this->volume_rebeches_sur_place[$key] += $noeud->getSurPlaceRebeches();
                    }
                }

                if($exclude) {
                    unset($this->appellations[$key]);
                }

            /*}*/
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
        if(isset($this->volume_vci)) {
            $this->total_volume_vci =  array_sum(array_values($this->volume_vci));
        }
        if(isset($this->volume_vci_sur_place) && $this->dr->recolte->getTotalVolumeVendus() > 0 && $this->can_calcul_volume_revendique_sur_place) {
            $this->total_volume_vci_sur_place =  array_sum(array_values($this->volume_vci_sur_place));
        }
        $this->lies = $this->dr->lies;
        $this->jeunes_vignes = $this->dr->jeunes_vignes;

        $this->vintable = array();
        if ($this->dr->exist('recolte/certification/genre/appellation_VINTABLE')) {
          $this->vintable['superficie'] = $this->dr->recolte->certification->genre->appellation_VINTABLE->getTotalSuperficie();
          $this->vintable['volume'] = $this->dr->recolte->certification->genre->appellation_VINTABLE->getTotalVolume();
        }
        $this->annee = $this->dr->campagne;
    }
}
