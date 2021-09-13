<?php
/**
 * Model for DSCepage
 *
 */

class DSCepage extends BaseDSCepage {

    public function getCouleur() {

        return $this->getParent();
    }

    public function getCepage() {

        return $this;
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

    public function getChildrenNodeSorted() {

        return $this->getChildrenNode();
    }

    public function getProduits() {

        return array($this->getHash() => $this);
    }

    public function getProduitsSorted() {

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

    public function getProduitsDetailsSorted() {

      return $this->getProduitsDetails();
    }

    public function addDetail($lieu_dit = null) {
        if($detail = $this->getDetailNode($lieu_dit)) {

            return $detail;
        }
        $detail = $this->detail->add();

        $detail->lieu = $lieu_dit;
        return $detail;
    }


    public function hasLieu($lieu) {
        return !is_null($this->getDetailLieu($lieu));
    }

    public function getDetailNode($lieu = null) {
        foreach ($this->getProduitsDetails() as $d) {
            if(is_null($lieu)) {

                return $d;
            }

            if($d->exist('lieu') && trim(strtolower($d->lieu) == trim(strtolower($lieu)))) {

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

    public function addVolumes($lieu,$vol_normal,$vol_vt,$vol_sgn,$sum=false) {
        $detail = $this->addDetail($lieu);
        if($vol_normal > 0.0)
            $detail->updateVolume(DSCivaClient::VOLUME_NORMAL,$vol_normal, $sum);
        if($vol_vt > 0.0)
            $detail->updateVolume(DSCivaClient::VOLUME_VT,$vol_vt, $sum);
        if($vol_sgn > 0.0)
            $detail->updateVolume(DSCivaClient::VOLUME_SGN,$vol_sgn, $sum);
        return $detail;
    }

    public function updateTotalVolumes() {
        $this->total_normal = 0;
        $this->total_vt = 0;
        $this->total_sgn = 0;
        $this->total_stock = 0;
        foreach($this->getChildrenNode() as $item) {
            $this->total_normal += $item->volume_normal;
            $this->total_vt += $item->volume_vt;
            $this->total_sgn += $item->volume_sgn;
        }

        $this->total_stock = $this->total_normal + $this->total_vt + $this->total_sgn;
    }

    public function checkNoVTSGNImport($vol_vt,$vol_sgn) {
        if($this->no_vtsgn && (($vol_vt > 0) || ($vol_sgn > 0)))
            return false;
        return true;
    }

    public function cleanAllNodes() {
        $details = $this->getProduitsDetails();
        foreach ($details as $detail) {
            if($detail->isVide()){
                $detail->delete();
            }
        }
    }

     public function getCodeDouane($vtsgn = '') {
            return $this->getConfig()->getDouane()->getFullAppCode($vtsgn).$this->getConfig()->getDouane()->getCodeCepage();
            }
     }
