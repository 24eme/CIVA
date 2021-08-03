<?php

abstract class _DSNoeud extends acCouchdbDocumentTree {

    public function getConfig() {

        return $this->getDocument()->getConfig()->get(HashMapper::convert($this->getHash()));
    }

    abstract public function getChildrenNode();

    public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $items_config = $this->getConfig()->getChildrenNode();
        $items_sorted = array();

        foreach($items_config as $hashConfig => $item_config) {
            $hashDS = HashMapper::inverse($item_config->getHash(), 'DS');
            if($this->getDocument()->exist($hashDS)) {
                $item = $this->getDocument()->get($hashDS);
                $items_sorted[$item->getHash()] = $item;
            }
        }

        return $items_sorted;
    }

    public function getChildrenNodeDeep($level = 1) {
      if(count($this->getChildrenNode()) > 1) {

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
        if(!$this->exist('libelle_long') || is_null($this->_get('libelle_long'))) {
            $this->add('libelle_long', $this->getConfig()->getLibelleLong());
        }

        return $this->_get('libelle_long');
    }

    public function hasLieuEditable() {

        return $this->getConfig()->hasLieuEditable();
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

    public function hasVolume() {
        return ($this->total_stock && $this->total_stock > 0);
    }

    public function update($params = array()) {
        parent::update();
        $this->updateTotalVolumes();
    }

    public function cleanAllNodes() {
        $to_be_removed = array();
        foreach($this->getChildrenNodeSorted() as $hash => $item) {
            $item->cleanAllNodes();
            if(!count($item->getProduitsDetails())){
                $to_be_removed[] = $hash;
            }
        }
        foreach($to_be_removed as $hash) {
            $this->getDocument()->remove($hash);
        }
    }

}
