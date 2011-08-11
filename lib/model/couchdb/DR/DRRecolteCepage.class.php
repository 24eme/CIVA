<?php

class DRRecolteCepage extends BaseDRRecolteCepage {

    public function getConfig() {
      return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }
    
    public function getLieu() {
        return $this->getParent();
    }

    public function getLibelle() {
        return $this->store('libelle', array($this, 'getInternalLibelle'));
    }
    
    public function getInternalLibelle() {
        return $this->getConfig()->getLibelle();
    }

    public function getCodeDouane($vtsgn = '') {
      return $this->getConfig()->getDouane()->getFullAppCode($vtsgn).$this->getConfig()->getDouane()->getCodeCepage();
    }

    public function getTotalVolume($force_calcul = false) {
      $field = 'total_volume';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array('volume'));
    }
    public function getTotalSuperficie($force_calcul = false) {
      $field = 'total_superficie';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array('superficie'));
    }

    public function getVolumeRevendique($force_calcul = false) {
      $field = 'volume_revendique';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
      }
      return $this->store($field, array($this, 'getVolumeRevendiqueFinal'));
    }

    public function getDplc($force_calcul = false) {
      $field = 'dplc';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
      }

      return $this->store($field, array($this, 'getDplcFinal'));
    }

    public function getTotalCaveParticuliere() {
        return $this->store('cave_particuliere', array($this, 'getSumDetailFields'), array('cave_particuliere'));
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

    public function getVolumeMax() {
      return ($this->total_superficie/100) * $this->getConfig()->getRendement();
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

    public function getArrayVtSgnDenomination($out = array()) {
        $resultat = array();
        if ($this->exist('detail')) {
            foreach($this->detail as $key => $item) {
                if (!in_array($key, $out)) {
                    $resultat[$key] = array('denomination' => null, 'vtsgn' => null);
                    $resultat[$key]['denomination'] = (string)$item->denomination;
                    $resultat[$key]['vtsgn'] = (string)$item->vtsgn;
                }
            }
        }
        return $resultat;
    }

    public function getArrayDenomination($out = array()) {
        $resultat = array();
        if ($this->exist('detail')) {
            foreach($this->detail as $key => $item) {
                if (!in_array($key, $out)) {
                    $resultat[$key]['denomination'] = (string)$item->denomination;
                }
            }
        }
        return $resultat;
    }

    protected function getDplcFinal() {
        if ($this->getConfig()->hasRendement() && $this->getCouchdbDocument()->canUpdate()) {
            $volume_max = $this->getVolumeMax();
            if ($this->total_volume > $volume_max) {
              return ($this->total_volume - $volume_max);
            } else {
              return 0;
            }
        } else {
            return $this->getSumDetailFields('volume_dplc');
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
            return $this->getSumDetailFields('volume_revendique');
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
