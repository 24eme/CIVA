<?php

class DRRecolteCepage extends BaseDRRecolteCepage {

    public function getCouleur() {

      return $this->getParent();
    }

    public function getLieu() {

      return $this->getCouleur()->getLieu();
    }

    public function getMention() {

      return $this->getLieu()->getMention();
    }

    public function getAppellation() {

      return $this->getLieu()->getAppellation();
    }

    public function getCepage() {

      return $this;
    }

    public function getChildrenNode() {

        return $this->detail;
    }

    public function getProduits() {

        return array($this->getHash() => $this);
    }

    public function getTotalSuperficieVendus() {
        if($this->hasRecapitulatif()) {
            return parent::getTotalSuperficieVendus();
        }

        return null;
    }

    public function getTotalDontVciVendus() {
        if($this->hasRecapitulatif()) {
            return parent::getTotalDontVciVendus();
        }

        return null;
    }

    public function getTotalDontVciVendusByType($type) {
        if($this->hasRecapitulatif()) {
            return parent::getTotalDontVciVendusByType($type);
        }

        return null;
    }

    public function getTotalSuperficieVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatif()) {
            return parent::getTotalSuperficieVendusByCvi($type, $cvi);
        }

        return null;
    }

    public function getTotalDontDplcVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatif()) {
            return parent::getTotalDontDplcVendusByCvi($type, $cvi);
        }

        return null;
    }

    public function getTotalDontVciVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatif()) {
            return parent::getTotalDontVciVendusByCvi($type, $cvi);
        }

        return null;
    }

    public function getProduitsDetails() {
      $details = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $details[$item->getHash()] = $item;
      }

      return $details;
    }

    public function getCodeDouane($vtsgn = '') {

        return $this->getConfig()->getCodeDouane($vtsgn);
    }

    public function getTotalRebeches() {
      if($this->getKey() != 'cepage_RB') {

        return null;
      }

      $volume = 0;

      foreach($this->getChildrenNode() as $item) {
        $volume += $item->total_volume;
      }

      return $volume;
    }

    public function getSurPlaceRebeches() {
      if($this->getKey() != 'cepage_RB') {

        return null;
      }

      $volume = 0;

      foreach($this->getChildrenNode() as $item) {
        $volume += $item->cave_particuliere;
      }

      return $volume;
    }


    public function getArrayUniqueKey($out = array()) {
        $resultat = array();
        if ($this->exist('detail')) {
            foreach($this->detail as $key => $item) {
                if (!in_array($key, $out)) {
                    $resultat[$key] = $item->getUniqueKey();
                }
            }
        }
        return $resultat;
    }

    public function getHashUniqueKey($out = array()) {
      $resultat = array();
      foreach ($this->getArrayUniqueKey($out) as $key => $item) {
	     $resultat[$item] = $this->detail[$key];
      }
      return $resultat;
    }

    public function retrieveDetailFromUniqueKeyOrCreateIt($denom, $vtsgn, $lieu = '') {
      $uk = DRRecolteCepageDetail::getUKey($lieu, $denom, $vtsgn);
      $hash = $this->getHashUniqueKey();
      if (isset($hash[$uk]))
    	return $hash[$uk];
      $ret = $this->detail->add();
      if($denom) {
        $ret->denomination = $denom;
      }
      if($vtsgn) {
          $ret->vtsgn = $vtsgn;
      }
      $ret->vtsgn = $vtsgn;
      if($lieu) {
        $ret->lieu = $lieu;
      }
      return $ret;
    }

    public function canHaveUsagesLiesSaisi() {

        return false;
    }

    public function canCalculSuperficieSurPlace() {
        if($this->hasRecapitulatif()) {
            return parent::canCalculSuperficieSurPlace();
        }

        return true;
    }

    public function canCalculVolumeRevendiqueSurPlace() {
        if($this->hasRecapitulatif()) {
            return parent::canCalculVolumeRevendiqueSurPlace();
        }

        return true;
    }

    protected function getSumNoeudFields($field, $exclude = true) {

        return parent::getSumNoeudFields($field, false);
    }

    protected function getSumNoeudWithMethod($method, $exclude = true) {

        return parent::getSumNoeudWithMethod($method, false);
    }

    public function cleanAllNodes() {

        return;
    }

    public function removeVolumes() {
      parent::removeVolumes();
      $details_to_remove = array();
      foreach($this->getProduitsDetails() as $detail) {
        if(!$detail->getTotalSuperficie()) {
          $details_to_remove[] = $detail->getKey();
        }
      }

      foreach ($details_to_remove as $key) {
        $this->detail->remove($key);
      }
    }

    public function hasRecapitulatif() {

        return !$this->getConfig()->existRendementAppellation() && !$this->getConfig()->existRendementCouleur() && $this->getConfig()->getRendementCepage() && !$this->getLieu()->getConfig()->getRendementCepage();
    }

    protected function update($params = array()) {
        if($this->hasRecapitulatif()) {
            $this->add('acheteurs');
        }

        if($this->exist('acheteurs')) {
            $this->preUpdateAcheteurs();
        }
        parent::update($params);

      if ($this->getCouchdbDocument()->canUpdate()) {
          $this->total_volume = $this->getTotalVolume(true);
          $this->total_superficie = $this->getTotalSuperficie(true);
          $this->dplc = $this->getDplc(true);
          $this->lies = $this->getLies(true);
          $this->usages_industriels = $this->getUsagesIndustriels(true);
          $this->volume_revendique = $this->getVolumeRevendique(true);
          $this->vci = $this->getTotalVci(true);
      }
      if($this->exist('acheteurs')) {
      $this->updateAcheteurs();
      }
    }

}
