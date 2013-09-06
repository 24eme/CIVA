<?php

class DRRecolteCepage extends BaseDRRecolteCepage {

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

    public function getCodeDouane($vtsgn = '') {
        
        return $this->getConfig()->getDouane()->getFullAppCode($vtsgn).$this->getConfig()->getDouane()->getCodeCepage();
    }

    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach($this->detail as $object) {
                $acheteurs = $object->getVolumeAcheteurs($type);
                foreach($acheteurs as $cvi => $quantite_vendue) {
                    if (!isset($this->_storage[$key][$cvi])) {
                        $this->_storage[$key][$cvi] = 0;
                    }
		    if ($quantite_vendue)
		      $this->_storage[$key][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_storage[$key];
    }

    public function getTotalVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "total_volume_acheteurs_" . $type;
        if (!isset($this->_storage[$key])) {
            $sum = 0;
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $volume) {
                $sum += $volume;
            }
            $this->_storage[$key] = $sum;
        }
        return $this->_storage[$key];
    }

    public function removeVolumes() {
      $this->total_volume = null;
      $this->volume_revendique = null;
      $this->dplc = null;
      foreach($this->getDetail() as $detail) {
	$detail->removeVolumes();
      }
    }

    public function isNonSaisie() {
      if (!$this->exist('detail') || !count($this->detail))
	return false;
      foreach($this->detail as $detail) {
	if(!$detail->isNonSaisie())
	  return false;
      }
      return true;
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
      $ret->denomination = $denom;
      $ret->vtsgn = $vtsgn;
      $ret->lieu = $lieu;
      return $ret;
    }

    public function canHaveUsagesLiesSaisi() {
        
        return false;
    }

    protected function update($params = array()) {
      parent::update($params);
      if ($this->getCouchdbDocument()->canUpdate()) {
          $this->total_volume = $this->getTotalVolume(true);
          $this->total_superficie = $this->getTotalSuperficie(true);
          $this->dplc = $this->getDplc(true);
          $this->lies = $this->getLies(true);
          $this->usages_industriels = $this->getUsagesIndustriels(true);
          $this->volume_revendique = $this->getVolumeRevendique(true);
      }
    }
}
