<?php

abstract class _DSNoeud extends acCouchdbDocumentTree {
    
    public function getConfig() {
        
        return $this->getCouchdbDocument()->getConfigurationCampagne()->get(preg_replace('/^\/declaration/', '/recolte', $this->getHash()));
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
        if($this->getChildrenNode()){
            foreach($this->getChildrenNode() as $item) {
                $produits = array_merge($produits, $item->getProduitsDetails());
            }
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
}