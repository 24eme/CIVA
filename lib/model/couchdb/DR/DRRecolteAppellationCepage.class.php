<?php

class DRRecolteAppellationCepage extends BaseDRRecolteAppellationCepage {

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

    public function addDetail($detail) {
      return $this->add(null, $detail);
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

      if ($this->hasRendement() && $this->getCouchdbDocument()->canUpdate()) {
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

    public function getVolumeMax() {
      return ($this->total_superficie/100) * $this->getRendement();
    }

    private function getSumDetailFields($field) {
      $sum = 0;
      foreach ($this->getData()->detail as $detail) {
	$sum += $detail->{$field};
      }
      return $sum;
    }
    public function getTotalVolume() {
      if ($r = parent::_get('total_volume'))
	return $r;
      return $this->getSumDetailFields('volume');
      
    }
    public function getTotalSuperficie() {
      if ($r = parent::_get('total_superficie'))
	return $r;
      return $this->getSumDetailFields('superficie');
    }

    public function getDplc() {
      return $this->getTotalDPLC();
    }

    public function getTotalDPLC() {
      if ($r = parent::_get('dplc'))
	return $r;
      return $this->getSumDetailFields('volume_dplc');
    }

    public function getVolumeRevendique() {
      return $this->getTotalVolumeRevendique();
    }

    public function getTotalVolumeRevendique() {
      if ($r = parent::_get('volume_revendique'))
	return $r;
      return $this->getSumDetailFields('volume_revendique');
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

    public function getTotalCaveParticuliere() {
        return $this->getSumDetailFields('cave_particuliere');
    }

    public function getRendement() {
      return $this->getConfig()->getRendement();
    }

    public function hasTotalCepage() {
      $cpt = 0;
      if (!$this->getRendement())
	return false;
      
      /*foreach($this->getParent()->filter('cepage_') as $c) {
	$cpt++;
	if ($cpt>2)
	  break;
      
      if ($cpt < 2)
	return false;
       */

      return $this->getConfig()->hasTotalCepage();
    }

    public function excludeTotal() {
      return $this->getConfig()->excludeTotal();
    }

    public function hasRendement() {
        return ($this->getRendement()>0);
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
}
