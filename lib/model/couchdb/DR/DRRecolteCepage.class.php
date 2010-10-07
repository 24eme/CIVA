<?php

class DRRecolteCepage extends BaseDRRecolteCepage {

    protected $_total_acheteurs_by_cvi = array();

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }
    
    public function getLieu() {
        return $this->getParent();
    }

    public function getLibelle() {
      return $this->getConfig()->getLibelle();
    }

    public function getCodeDouane($vtsgn = '') {
      return $this->getConfig()->getDouane()->getFullAppCode($vtsgn).$this->getConfig()->getDouane()->getCodeCepage();
    }

    public function getTotalVolume() {
      $field = 'total_volume';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array('volume'));
    }
    public function getTotalSuperficie() {
      $field = 'total_superficie';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array('superficie'));
    }

    public function getVolumeRevendique() {
      $field = 'volume_revendique';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array($field));
    }

    public function getDplc() {
      $field = 'dplc';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumDetailFields'), array('volume_dplc'));
    }

    public function getTotalCaveParticuliere() {
        return $this->store('cave_particuliere', array($this, 'getSumDetailFields'), array('cave_particuliere'));
    }

    public function getTotalAcheteursByCvi($field) {
        if (!isset($this->_total_acheteurs_by_cvi[$field])) {
            $this->_total_acheteurs_by_cvi[$field] = array();
            foreach($this->detail as $object) {
                $acheteurs = $object->getAcheteursValuesWithCvi($field);
                foreach($acheteurs as $cvi => $quantite_vendue) {
                    if (!isset($this->_total_acheteurs_by_cvi[$field][$cvi])) {
                        $this->_total_acheteurs_by_cvi[$field][$cvi] = 0;
                    }
		    if ($quantite_vendue)
		      $this->_total_acheteurs_by_cvi[$field][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_total_acheteurs_by_cvi[$field];
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

    protected function getSumDetailFields($field) {
      $sum = 0;
      foreach ($this->getData()->detail as $detail) {
	$sum += $detail->{$field};
      }
      return $sum;
    }

    protected function update($params = array()) {
      parent::update($params);
      $s = 0;
      $v = 0;
      foreach ($this->get('detail') as $key => $item) {
	$v += $item->getVolume();
	$s += $item->getSuperficie();
      }

      $this->set('total_volume', $v);
      $this->set('total_superficie', $s);

      if ($this->getConfig()->hasRendement() && $this->getCouchdbDocument()->canUpdate()) {
	$volume_max = $this->getVolumeMax();
	if ($this->total_volume > $volume_max) {
	  $this->volume_revendique = $volume_max;
	  $this->dplc = $this->total_volume - $volume_max;
	} else {
          $this->dplc = 0;
	  $this->volume_revendique = $this->total_volume;
	}
      }
    }
}
