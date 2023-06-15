<?php

abstract class _VracNoeud extends acCouchdbDocumentTree {

    public function getConfig() {

        return $this->getCouchdbDocument()->getConfiguration()->get(HashMapper::convert($this->getHash()));
    }

    abstract public function getChildrenNode();

    public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $items_config = $this->getConfig()->getChildrenNode();
        $items_sorted = array();

        foreach($items_config as $item_config) {
            $hash = HashMapper::inverse($item_config->getHash());
            if($this->getDocument()->exist($hash)) {
                $items_sorted[$hash] = $this->getDocument()->get($hash);
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

    public function getNbProduitsDetails() {
        return count($this->getProduitsDetails());
    }

    public function getPositionNouveauProduitDetail() {
        return $this->getNbProduitsDetails() + 1;
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

    public function getLibelleComplet() {
    	$libelle = $this->getParent()->getLibelleComplet();
    	return trim($libelle).' '.$this->libelle;
    }

    public function cleanAllNodes() {
        $keys_to_remove = array();
        foreach($this->getChildrenNode() as $item) {
            $item->cleanAllNodes();
            if(!count($item->getProduitsDetails())){
                $keys_to_remove[$item->getKey()] = $item->getKey();
            }
        }
        foreach($keys_to_remove as $key) {
            $this->remove($key);
        }
    }

    public function getTotalVolumeEnleve()
    {
    	$total = 0;
        foreach($this->getChildrenNode() as $key => $item) {
            $total += $item->getTotalVolumeEnleve();
        }
        return $total;
    }

    public function getTotalVolumePropose()
    {
    	$total = 0;
        foreach($this->getChildrenNode() as $key => $item) {
            $total += $item->getTotalVolumePropose();
        }
        return $total;
    }

    public function getTotalSurfacePropose()
    {
    	$total = 0;
        foreach($this->getChildrenNode() as $key => $item) {
            $total += $item->getTotalSurfacePropose();
        }
        return $total;
    }

    public function getTotalPrixEnleve()
    {
    	$total = 0;
        foreach($this->getChildrenNode() as $key => $item) {
            $total += $item->getTotalPrixEnleve();
        }
        return $total;
    }

    public function getTotalPrixPropose()
    {
    	$total = 0;
        foreach($this->getChildrenNode() as $key => $item) {
            $total += $item->getTotalPrixPropose();
        }
        return $total;
    }

    public function allProduitsClotures()
    {
    	$result = true;
        foreach($this->getChildrenNode() as $key => $item) {
        	if (!$item->allProduitsClotures()) {
        		$result = false;
        		break;
        	}
        }
        return $result;
    }

    public function hasRetiraisons()
    {
    	$result = false;
        foreach($this->getChildrenNode() as $key => $item) {
        	if ($item->hasRetiraisons()) {
        		$result = true;
        		break;
        	}
        }
        return $result;
    }

    public function autoFillRetiraisons($fillDate = true)
    {
        foreach($this->getChildrenNode() as $key => $item) {
        	$item->autoFillRetiraisons($fillDate);
        }
        return null;
    }

}
