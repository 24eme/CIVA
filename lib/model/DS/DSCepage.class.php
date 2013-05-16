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
    
    public function addProduit($produitCepage, $lieu_dit = '', $vt=null, $sgn=null){
        $prod  = ConfigurationClient::getConfiguration()->get($produitCepage->getHash());
        $this->no_vtsgn = (int) ($prod->exist('no_vtsgn') && ($prod->no_vtsgn == '1'));
        $this->cepage = $produitCepage->getLibelle();
        $this->appellation = $produitCepage->getParent()->getParent()->getParent()->getParent()->getLibelle();
        $this->getCouleur()->getLieu()->getMention()->getAppellation()->appellation = $this->appellation;
        $this->addDetails($produitCepage);        
    }
    
    public function addDetails($produitCepage) {
        foreach ($produitCepage->getProduitsDetails() as $detail) {
            $this->addDetail($detail,$produitCepage,$this->no_vtsgn);
        }              
    }
        
    public function addDetail($detail,$produitCepage,$no_vtsgn = null) {
        $detail->lieu = ($detail->lieu)? $detail->lieu : '';
        if($node = $this->getDetailNode($detail->lieu)) return $node;
        $detailDS = $this->detail->add();
        $detailDS->volume_normal = 0;
        if(!$no_vtsgn){
            $detailDS->volume_vt = 0;
            $detailDS->volume_sgn = 0;
        }
        $detailDS->lieu = $detail->lieu;
        $detailDS->cepage = $produitCepage->getLibelle();
        $detailDS->appellation = $produitCepage->getParent()->getParent()->getParent()->getParent()->getLibelle();
    }

    public function hasLieu($lieu) {
        return !is_null($this->getDetailLieu($lieu));
    }
    
    public function getDetailNode($lieu) {
        foreach ($this->detail as $d) {
            if($d->exist('lieu') && $d->lieu == $lieu)                
                return $d;
        }
        return null;
    }
    
    public function getDetailLieu($lieu) {
        foreach ($this->detail as $d) {
            if($d->exist('lieu') && $d->lieu == $lieu)                
                return $d;
        }
        return null;
    }   
    
    public function getLieuDit() {
         foreach ($this->detail as $detail) {
             if($detail->exist('lieu') && $detail->lieu != '') return $detail->lieu;
         }
         return '';
    }
    
}