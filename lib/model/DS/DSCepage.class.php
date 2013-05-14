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

    public function updateVolume($field,$volume,$lieu=null){
        $details = $this->getProduitsDetails();              
        if(!count($details)){            
            throw new sfException("Le produit d'appellation $this->appellation et de cépage $this->cepage ne possède pas de détail.");
        }
        if(!$lieu){
            foreach ($details as $detail) {
                if(($detail->vtsgn == strtoupper($field)) || (($field == 'normal') && ($detail->vtsgn == ""))){
                    $volName = 'volume_'.$field; 
                    $this->getCouleur()->updateVolumes($detail->vtsgn,$detail->$volName,$volume);
                    $detail->updateVolume($field,$volume);
                }
            }
        }else{
            foreach ($this->detail->toArray() as $detail) {
                var_dump($detail->lieu);
                if(KeyInflector::slugify($detail->lieu) == $lieu){
//                    if(($detail->vtsgn == strtoupper($field)) || (($field == 'volume') && ($detail->vtsgn == ""))){
//                        $this->getCouleur()->updateVolumes($detail->vtsgn,$detail->volume,$volume);
//                        $detail->updateVolume($volume);
//                    }
                }
            }
             exit;
        }
        
    }
    
    public function getVolume() {
        if(!count($this->getProduitsDetails())){            
            throw new sfException("Le produit d'appellation $this->appellation et de cépage $this->cepage ne possède pas de détail.");
        }
        if(count($this->getProduitsDetails())== 1){
            $details = $this->getProduitsDetails(); 
            return $details[$this->getHash().'/detail/0']->volume_normal;
        }
        foreach ($this->getProduitsDetails() as $detail) {
            if($detail->vtsgn == DSCivaClient::VOLUME_NORMAL) return $detail->volume_normal;
        }
        return '0.00';
    }
    
    public function getVT() {
        if(!count($this->getProduitsDetails())){            
            throw new sfException("Le produit d'appellation $this->appellation et de cépage $this->cepage ne possède pas de détail.");
        }
        foreach ($this->getProduitsDetails() as $detail) {
            if($detail->vtsgn == DSCivaClient::VOLUME_VT) return $detail->volume_vt;
        }
        return '0.00';
    }
    
    public function getSGN() {
        if(!count($this->getProduitsDetails())){            
            throw new sfException("Le produit d'appellation $this->appellation et de cépage $this->cepage ne possède pas de détail.");
        }
        foreach ($this->getProduitsDetails() as $detail) {
            if($detail->vtsgn==DSCivaClient::VOLUME_SGN) return $detail->volume_sgn;
        }
        return '0.00';
    }
    
    public function getProduitsByLieuDits() {
        $detail_lieux_dit = array();
        foreach ($this->getProduitsDetails() as $detail) {
                if(!isset($detail_lieux_dit[$detail->lieu]))
                    $detail_lieux_dit[$detail->lieu] = array();
                $detail_lieux_dit[$detail->lieu][] = $detail;
        }
        return $detail_lieux_dit;         
    }
    
    public function getLieuDit() {
         foreach ($this->detail as $detail) {
             if($detail->exist('lieu') && $detail->lieu != '') return $detail->lieu;
         }
         return '';
    }
    
}