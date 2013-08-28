<?php

abstract class _DRRecolteNoeud extends acCouchdbDocumentTree {

    public function getConfig() {

        return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    abstract public function getChildrenNode();

    public function getChildrenNodeDeep($level = 1) {
      if($this->getConfig()->hasManyNoeuds()) {
          
          throw new sfException("getChildrenNodeDeep() peut uniquement être appelé d'un noeud qui contient un seul enfant...");
      }

      $node = $this->getChildrenNode()->getFirst();
      
      if($level > 1) {
        
        return $node->getChildrenNodeDeep($level - 1);
      }

      return $node->getChildrenNode();
    }

    public function getProduits() {
        $produits = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $produits = array_merge($produits, $item->getProduits());
        }

        return $produits;
    }

    public function getProduitsDetails() {
        $produits = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $produits = array_merge($produits, $item->getProduitsDetails());
        }
        return $produits;
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
            return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100), 0);
        } else {
            return 0;
        }
    }
    
    public function getTotalSuperficie($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_superficie", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalVolume($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_volume", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalCaveParticuliere() {

        return $this->getDataByFieldAndMethod('total_cave_particuliere', array($this, 'getSumNoeudWithMethod'), true, array('getTotalCaveParticuliere') );
    }

    public function getDplc($force_calcul = false) {
        if(!$this->getConfig()->hasRendementNoeud()) {

            return $this->getDataByFieldAndMethod("dplc", array($this,"getDplcTotal") , $force_calcul);
        }
        
        return $this->getDataByFieldAndMethod('dplc', array($this, 'findDplc'), $force_calcul);
    }

    public function getDplcTotal() {

        return $this->getDataByFieldAndMethod('dplc_total', array($this, 'getSumNoeudFields'),true, array('dplc'));
    }

    public function findDplc() {
        $dplc_total = $this->getDplcTotal();
        $dplc = $dplc_total;
        if ($this->getConfig()->hasRendementNoeud()) {
            $dplc_rendement = $this->getDplcRendement();
            if ($dplc_total < $dplc_rendement) {
                $dplc = $dplc_rendement;
            }
        }
        return $dplc;
    }

    public function getDplcRendement() {
        $key = "dplc_rendement";
        if (!isset($this->_storage[$key])) {
            $volume_dplc = 0;
            if ($this->getConfig()->hasRendementNoeud()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxRendement();
                if ($volume > $volume_max) {
                    $volume_dplc = $volume - $volume_max;
                } else {
                    $volume_dplc = 0;
                }
            }
            $this->_storage[$key] = round($volume_dplc, 2);
        }
        return $this->_storage[$key];
    }

    public function getVolumeMaxRendement() {
            
        return round(($this->getTotalSuperficie() / 100) * $this->getConfig()->getRendementNoeud(), 2);
    }

    public function getVolumeRevendique($force_calcul = false) {
        if(!$this->getConfig()->hasRendementNoeud()) {

            return $this->getDataByFieldAndMethod('volume_revendique', array($this, 'getVolumeRevendiqueTotal'), $force_calcul);
        }
        
        return $this->getDataByFieldAndMethod('volume_revendique', array($this, 'findVolumeRevendique'), $force_calcul);
    }

    public function getVolumeRevendiqueTotal($force_calcul = false) {

        return $this->getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul);
    }

    public function findVolumeRevendique() {

        return round(min($this->getVolumeRevendiqueWithDplc(), $this->getVolumeRevendiqueWithUI()), 2);
    }

    public function getVolumeRevendiqueWithDplc() {
        
        return $this->getTotalVolume() - $this->getDplc();
    }

    public function getVolumeRevendiqueWithUI() {
        
        return $this->getTotalVolume() - $this->getUsagesIndustriels();
    }

    public function getUsagesIndustriels($force_calcul = false) {
        if(!$this->canHaveUsagesIndustrielsSaisi()) {

            return $this->getDataByFieldAndMethod('usages_industriels', array($this, 'getUsagesIndustrielsTotal'), $force_calcul);
        }

        return $this->_get('usages_industriels') ? $this->_get('usages_industriels') : 0;
    }

    public function getUsagesIndustrielsTotal() {

        return $this->getDataByFieldAndMethod('usages_industriels_total', array($this, 'getSumNoeudFields'), true, array('usages_industriels'));
    }

    public function canHaveUsagesIndustrielsSaisi() {

        return false;
    }

    protected function getSumNoeudFields($field, $exclude = true) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $key => $noeud) {
            if($exclude && $noeud->getConfig()->excludeTotal()) {

                continue;
            }
            
            $sum += $noeud->get($field);
        }
        return $sum;
    }

    public function isUsagesIndustrielsSaisiCepage() {

        return $this->getDocument()->exist('usages_industriels_cepage') && $this->getDocument()->get('usages_industriels_cepage');
    }

    public function getLibelle() {

        return $this->store('libelle', array($this, 'findLibelle'));
    }

    protected function findLibelle() {

        return $this->getConfig()->getLibelle();
    }

    protected function getSumFields($field) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $k => $noeud) {
            $sum += $noeud->get($field);
        }
        return $sum;
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }

    protected function getDataByFieldAndMethod($field, $method, $force_calcul = false, $parameters = array()) {
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }

        if(!empty($parameters))
            return $this->store($field, $method, $parameters);

        return $this->store($field, $method, array($field));
    }

    protected function getSumNoeudWithMethod($method, $exclude = true) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $noeud) {
            if($exclude && $noeud->getConfig()->excludeTotal()) {

                continue;
            }

            $sum += $noeud->$method();
        }
        return $sum;
    }

}