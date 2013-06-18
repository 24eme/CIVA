<?php

abstract class _DSNoeud extends acCouchdbDocumentTree {
    
    public function getConfig() {
        
        return $this->getCouchdbDocument()->getConfigurationCampagne()->get(preg_replace('/^\/declaration/', '/recolte', $this->getHash()));
    }

    abstract public function getChildrenNode();

    public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $items_config = $this->getConfig()->getChildrenNode();
        $items_sorted = array();

        foreach($items_config as $hash => $item_config) {
            $hash = preg_replace('/^\/recolte/','declaration',$hash);
            if($this->exist($item_config->getKey())) {
                $items_sorted[$hash] = $this->get($item_config->getKey());
            }
        }

        return $items_sorted;
    }

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
        foreach($this->getChildrenNode() as $item) {
            $produits = array_merge($produits, $item->getProduitsDetails());
        }

        return $produits;
    }

    public function getProduitsDetailsSorted() {
        $produits = array();
        foreach($this->getChildrenNodeSorted() as $item) {
            $produits = array_merge($produits, $item->getProduitsDetailsSorted());
        }

        return $produits;
    }

    public function hasManyNoeuds(){
        if(count($this->getChildrenNode()) > 1){
            return true;
        }
        return false;
    }
    
     public function getProduitsWithVolume() {
        $produits_with_volume = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $produits_with_volume = array_merge($produits_with_volume, $item->getProduitsWithVolume());
        }

        return $produits_with_volume;
    }

    public function getLibelle() {
        if(is_null($this->_get('libelle'))) {
            $this->_set('libelle', $this->getConfig()->getLibelle());
        }

        return $this->_get('libelle');
    }

    public function getLibelleLong() {
        if(is_null($this->_get('libelle_long'))) {
            $this->_set('libelle_long', $this->getConfig()->getLibelleLong());
        }

        return $this->_get('libelle_long');
    }
    
    public function updateVolumes($vtsgn,$old_volume,$volume) {
        switch ($vtsgn) {
            case DSCivaClient::VOLUME_VT:
                $this->total_vt += $volume - $old_volume;
            break;

            case DSCivaClient::VOLUME_SGN:
                $this->total_sgn += $volume - $old_volume;
            break;

            case DSCivaClient::VOLUME_NORMAL:
                $this->total_normal += $volume - $old_volume;
            break;

            default:
                break;
        }
        $this->total_stock += $volume - $old_volume;
    }

    public function updateTotalVolumes() {
        $this->total_normal = 0;
        $this->total_vt = 0;
        $this->total_sgn = 0;
        $this->total_stock = 0;
        foreach($this->getChildrenNode() as $item) {
            $this->total_stock += $item->total_stock;
            $this->total_normal += $item->total_normal;
            $this->total_vt += $item->total_vt;
            $this->total_sgn += $item->total_sgn;
        }
    }

    public function update($params = array()) {
        parent::update();
        $this->updateTotalVolumes();
    }
    
    public function cleanAllNodes() {   
        foreach($this->getChildrenNodeSorted() as $item) {
            $item->cleanAllNodes();  
            if(!count($item->getProduitsDetails())){
                $this->remove($item->getKey());
            }
        }
    }
    
//    public function removeNodesForClean() {        
//        if(is_null($this->getParent()->total_normal) && is_null($this->getParent()->total_vt) && is_null($this->getParent()->total_sgn)){
//            if(!$this->getParent()->removeNodesForClean()){
//            $this->delete();
//            return true;            
//            }
//        return false;
//        }
//    }
    
}