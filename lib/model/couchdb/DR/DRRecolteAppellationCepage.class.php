<?php

class DRRecolteAppellationCepage extends BaseDRRecolteAppellationCepage {

    protected $_total_acheteurs_by_cvi = array();

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function addDetail($detail) {
      return $this->add(null, $detail);
    }
    protected function update() {
      parent::update();
      $s = 0;
      $v = 0;
      foreach ($this->get('detail') as $key => $item) {
	$v += $item->getVolume();
	$s += $item->getSuperficie();
      }

      $this->set('total_volume', $v);
      $this->set('total_superficie', $s);
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
        return ConfigurationClient::getConfiguration()->get($this->getHash())->getRendement();
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
	  return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100),0);
        } else {
            return 0;
        }
    }

    public function removeVolumes() {
      foreach($this->getDetail() as $detail) {
	$detail->removeVolumes();
      }
    }
}
