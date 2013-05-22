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

    public function getMention() {

        return $this->getCouleur()->getLieu();
    }

    public function getAppellation() {

        return $this->getLieu()->getAppellation();
    }

    public function getChildrenNode() {

        return $this->detail;
    }

    public function getProduits() {
      
        return array($this->getHash() => $this);
    }

    public function hasVtsgn() {

        return (bool) $this->no_vtsgn;
    }

    public function getProduitsDetails() {
      $details = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $details[$item->getHash()] = $item;
      } 
      return $details;
    }
    
    public function addDetail($lieu_dit = null) {
        if($detail = $this->getDetailNode($lieu_dit)) {

            return $detail;
        }

        $detail = $this->detail->add();
        $detail->volume_normal = 0;
        if($this->hasVtsgn()){
            $detail->volume_vt = 0;
            $detail->volume_sgn = 0;
        }

        $detail->lieu = $lieu_dit;
        return $detail;
    }


    public function hasLieu($lieu) {
        return !is_null($this->getDetailLieu($lieu));
    }
    
    public function getDetailNode($lieu = null) {
        foreach ($this->detail as $d) {
            if(is_null($lieu)) {

                return $d;
            }

            if($d->exist('lieu') && $d->lieu == $lieu) {                
             
                return $d;
            }
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