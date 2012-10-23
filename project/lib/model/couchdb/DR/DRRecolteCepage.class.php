<?php

class DRRecolteCepage extends BaseDRRecolteCepage {

    public function getCouleur() {

      return $this->getParent();
    }

    public function getLieu() {

      return $this->getCouleur()->getLieu();
    }

    public function getNoeuds() {

        return $this->detail;
    }

    public function getCodeDouane($vtsgn = '') {
      return $this->getConfig()->getDouane()->getFullAppCode($vtsgn).$this->getConfig()->getDouane()->getCodeCepage();
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique',  array($this, 'getVolumeRevendiqueFinal'), $force_calcul );
    }

    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod('dplc',  array($this, 'getDplcFinal'), $force_calcul);

    }

    public function getTotalCaveParticuliere() {
        return $this->store('cave_particuliere', array($this, 'getSumDetailFields'), array('cave_particuliere'));
    }

    public function getTotalVolume($force_calcul = false) {

        return parent::getDataByFieldAndMethod('total_volume',  array($this, 'getSumNoeudFields'), $force_calcul,  array('volume'));

    }

    public function getTotalSuperficie($force_calcul = false) {

        return parent::getDataByFieldAndMethod('total_superficie',  array($this, 'getSumNoeudFields'), $force_calcul,  array('superficie'));

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

    public function getVolumeMax() {
      return round(($this->total_superficie/100) * $this->getConfig()->getRendement(), 2);
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
	  return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100),0);
        } else {
            return 0;
        }
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

    protected function getDplcFinal() {
        if ($this->getConfig()->hasRendement() && $this->getCouchdbDocument()->canUpdate()) {
            $volume_max = $this->getVolumeMax();
            if ($this->total_volume > $volume_max) {
              return round($this->total_volume - $volume_max, 2);
            } else {
              return 0;
            }
        } else {
            return $this->getSumNoeudFields('volume_dplc');
        }
    }

    protected function getVolumeRevendiqueFinal() {
        if ($this->getConfig()->hasRendement() && $this->getCouchdbDocument()->canUpdate()) {
            $volume_max = $this->getVolumeMax();
            if ($this->total_volume > $volume_max) {
              return $volume_max;
            } else {
              return $this->total_volume;
            }
        } else {
            return $this->getSumNoeudFields('volume_revendique');
        }
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }

    protected function getSumDetailFields($field) {
      $sum = 0;
      foreach ($this->detail as $detail) {
	        $sum += $detail->get($field);
      }
      return $sum;
    }

    protected function update($params = array()) {
      parent::update($params);
      if ($this->getCouchdbDocument()->canUpdate()) {
          $this->total_volume = $this->getTotalVolume(true);
          $this->total_superficie = $this->getTotalSuperficie(true);
          $this->volume_revendique = $this->getVolumeRevendique(true);
          $this->dplc = $this->getDplc(true);
      }
    }
}
