<?php
/**
 * Model for DSCepage
 *
 */

class DSCepage extends BaseDSCepage {
    
    public function getCouleur() {

      return $this->getParent();
    }

    public function getLieu() {

      return $this->getCouleur()->getLieu();
    }

    public function getChildrenNode() {

        return $this->detail;
    }

    public function getProduits() {
      
        return array($this->getHash() => $this);
    }

    public function getProduitsDetails() {
      $details = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $details[$item->getHash()] = $item;
      }

      return $details;
    }
    
    public function addProduit($produitCepage,$lieu_dit = '', $vt=null, $sgn=null){
        $this->cepage = $produitCepage->getLibelle();
        $this->appellation = $produitCepage->getParent()->getParent()->getParent()->getParent()->getLibelle();
        foreach ($produitCepage->getProduitsDetails() as $detail) {
           
            $detailDS = $this->detail->add();
            $detailDS->vtsgn = $detail->vtsgn;
            $detailDS->volume = 0;
            if($detail->exist('lieu') && $detail->lieu != ''){
                $detailDS->add('lieu',$detail->lieu);
            }
            $detailDS->cepage = $produitCepage->getLibelle();
            $detailDS->appellation = $produitCepage->getParent()->getParent()->getParent()->getParent()->getLibelle();
        }
    }
    
}