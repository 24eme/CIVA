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

    public function executeRecapDeclaration(sfWebRequest $request) {
        
        $tiers = $this->getUser()->getTiers();
        $annee = $this->getRequestParameter('annee', null);
        $key = 'DR-'.$tiers->cvi.'-'.$annee;
        $dr = sfCouchdbManager::getClient()->retrieveDocumentById($key);
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

    }


}
