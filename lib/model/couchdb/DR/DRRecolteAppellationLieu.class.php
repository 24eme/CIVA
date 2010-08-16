<?php

class DRRecolteAppellationLieu extends BaseDRRecolteAppellationLieu {

    protected $_total_acheteurs_by_cvi = array();

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function getLibelle() {
        return ConfigurationClient::getConfiguration()->get($this->getHash())->getLibelle();
    }

    public function getVolumeAcheteur($cvi, $type) {
        $sum = 0;
        foreach ($this->getAcheteursFromCepage($type) as $a) {
            if ($a->cvi == $cvi)
                $sum += $a->quantite_vendue;
        }
        return $sum;
    }

    public function getTotalVolume() {
      $r = $this->_get('total_volume');
      if ($r)
	return $r;
      return $this->getSumCepageFields('total_volume');
      
    }
    public function getTotalSuperficie() {
      $r =  $this->_get('total_superficie');
      if ($r)
	return $r;
      return $this->getSumCepageFields('total_superficie');
    }

    public function getTotalVolumeRevendique() {
      $r =  $this->_get('total_superficie');
      if ($r)
	return $r;
      return $this->getSumCepageFields('volume_revendique');
    }

    public function getTotalDPLC() {
      $r = $this->_get('dplc');
      if ($r)
	return $r;
      return $this->getSumCepageFields('dplc');
    }

    public function getTotalCaveParticuliere() {
      $sum = 0;
      foreach ($this->filter('^cepage') as $key => $cepage) {
	$sum += $cepage->getTotalCaveParticuliere();
      }
      return $sum;
    }
    
    private function getSumCepageFields($field) {
      $sum = 0;
      foreach ($this->filter('^cepage') as $key => $cepage) {
	$sum += $cepage->get($field);
      }
      return $sum;
    }


    private function getAcheteursFromCepage($type = 'negoces|cooperatives') {
        $acheteurs = array();
        foreach ($this->filter('^cepage') as $key => $cepage) {
            foreach ($cepage->detail as $key => $d) {
                foreach ($d->filter($type) as $key => $t) {
                    foreach ($t as $key => $a) {
                        array_push($acheteurs, $a);
                    }
                }
            }
        }
        return $acheteurs;
    }

    public function getTotalAcheteursByCvi($field) {
        if (!isset($this->_total_acheteurs_by_cvi[$field])) {
            $this->_total_acheteurs_by_cvi[$field] = array();
            foreach ($this->filter('^cepage') as $object) {
                $acheteurs = $object->getTotalAcheteursByCvi($field);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
                    if (!isset($this->_total_acheteurs_by_cvi[$field][$cvi])) {
                        $this->_total_acheteurs_by_cvi[$field][$cvi] = 0;
                    }
                    $this->_total_acheteurs_by_cvi[$field][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_total_acheteurs_by_cvi[$field];
    }

    public function update() {
        parent::update();
        foreach ($this->getAcheteursFromCepage() as $a) {
            $acheteur = $this->add('acheteurs')->add($a->cvi);
            $acheteur->type_acheteur = $a->getParent()->getKey();
        }
    }
    public function save() {
      return $this->getCouchdbDocument()->save();
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
	  return round($this->getTotalVolume() / ($this->getTotalSuperficie() /100),0);
        } else {
            return 0;
        }
    }
    public function removeVolumes() {
      foreach($this->filter('^cepage_') as $cepage) {
	$cepage->removeVolumes();
      }
    }
}
