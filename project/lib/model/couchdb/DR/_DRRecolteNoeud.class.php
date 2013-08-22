<?php

abstract class _DRRecolteNoeud extends acCouchdbDocumentTree {

    const USAGES_INDUSTRIELS_NOEUD_LIEU = 'lieu';
    const USAGES_INDUSTRIELS_NOEUD_DETAIL = 'detail';

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
    
    public function getTotalSuperficie($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_superficie", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalVolume($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_volume", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalCaveParticuliere() {

        return $this->getDataByFieldAndMethod('total_cave_particuliere', array($this, 'getSumNoeudWithMethod'), true, array('getTotalCaveParticuliere') );
    }

    public function getDataByFieldAndMethod($field, $method, $force_calcul = false, $parameters = array()) {
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }

        if(!empty($parameters))
            return $this->store($field, $method, $parameters);

        return $this->store($field, $method, array($field));
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


    public function getLibelle() {

        return $this->store('libelle', array($this, 'getInternalLibelle'));
    }

    public function getInternalLibelle() {

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

    public function getUsagesIndustrielsNoeud() {

        return $this->getDocument()->recolte->_get('usages_industriels_noeud');
    }

}