<?php

abstract class _DSNoeud extends acCouchdbDocumentTree {
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
}