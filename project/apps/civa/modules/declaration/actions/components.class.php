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
	$this->has_import =  sfCouchdbManager::getClient('CSV')->countCSVsFromRecoltant($this->getUser()->getTiers()->cvi);
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
        
        $tiers = $this->getUser()->getTiers('Recoltant');
        $annee = $this->getRequestParameter('annee', $this->getUser()->getCampagne());
        $key = 'DR-'.$tiers->cvi.'-'.$annee;

        if(isset($this->visualisation_avant_import) && $this->visualisation_avant_import == true )
        {
            $import_from = array();
            $dr = sfCouchdbManager::getClient('DR')->createFromCSVRecoltant($tiers, $import_from);
        }else{
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
        }

        $this->appellations = array();
        $this->superficie = array();
        $this->volume = array();
        $this->revendique = array();
        $this->usages_industriels = array();
        $this->libelle = array();
        $this->volume_negoces = array();
        $this->volume_cooperatives = array();
        $this->volume_sur_place = array();
        $cvi = array();
        foreach ($dr->recolte->certification->genre->getConfig()->filter('^appellation_') as $appellation_key => $appellation_config) {
          if ($dr->recolte->certification->genre->exist($appellation_key)) {
              $appellation = $dr->recolte->certification->genre->get($appellation_key);
              if ($appellation->getConfig()->excludeTotal())
                continue;
              $this->appellations[] = $appellation->getAppellation();
              $this->libelle[$appellation->getAppellation()] = $appellation->getConfig()->getLibelle();
              $this->superficie[$appellation->getAppellation()] = $appellation->getTotalSuperficie();
              $this->volume[$appellation->getAppellation()] = $appellation->getTotalVolume();
              $this->revendique[$appellation->getAppellation()] = $appellation->getVolumeRevendique();
              $this->usages_industriels[$appellation->getAppellation()] = $appellation->getTotalUsagesIndustriels();
              $this->volume_sur_place[$appellation->getAppellation()] = $appellation->getTotalCaveParticuliere();

            }
        }
        $this->total_superficie = array_sum(array_values($this->superficie));
        $this->total_volume = array_sum(array_values($this->volume));
        $this->total_dplc = array_sum(array_values($this->usages_industriels));
        $this->total_revendique = array_sum(array_values($this->revendique));
        $this->total_volume_sur_place = array_sum(array_values($this->volume_sur_place));

        $this->lies = $dr->lies;
        $this->jeunes_vignes = $dr->jeunes_vignes;
	
	$this->vintable = array();
	if ($dr->recolte->certification->genre->exist('appellation_VINTABLE')) {
	  $this->vintable['superficie'] = $dr->recolte->certification->genre->appellation_VINTABLE->getTotalSuperficie();
	  $this->vintable['volume'] = $dr->recolte->certification->genre->appellation_VINTABLE->getTotalVolume();
	}
        $this->annee = $annee;
    }

}
