<?php

abstract class _VracNoeud extends acCouchdbDocumentTree {
    
    public function getConfig() {
        
        return $this->getCouchdbDocument()->getConfiguration()->get(preg_replace('/^\/declaration/', '/recolte', $this->getHash()));
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
    
    
    public function getProduitsSorted() {
        $produits = array();
        foreach($this->getChildrenNodeSorted() as $key => $item) {
            $produits = array_merge($produits, $item->getProduitsSorted());
        }

        return $produits;
    }
    
    public function getProduitsSortedWithFilter($matches) {
        $produits = $this->getProduitsSorted();
        $result = array();
        foreach($produits as $hash => $produit) {
            foreach($matches as $match) {
                if(preg_match('/'.$match.'/',$hash)){
                    $result[$hash] = $produit;
                }
            }
        }
        return $result;
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
    
    public function cleanAllNodes() {   
        foreach($this->getChildrenNodeSorted() as $item) {
            $item->cleanAllNodes();  
            if(!count($item->getProduitsDetails())){
                $this->remove($item->getKey());
            }
        }
    }    
    
}