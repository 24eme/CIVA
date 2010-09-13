<?php

class DRRecolteAppellationLieu extends BaseDRRecolteAppellationLieu {

    protected $_total_acheteurs_by_cvi = array();

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function getCodeDouane($vtsgn = '') {
      if ($this->getParent()->getKey() == 'appellation_VINTABLE') {
	if ($this->getParent()->getParent()->filter('appellation_')->count() > 1) {
	  $vtsgn = 'AOC';
	}
      }
      return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
    }

    public function getAppellation() {
      return $this->getParent()->getAppellation();
    }

    public function getLibelleWithAppellation() {
      if ($this->getLibelle())
	return $this->getParent()->getLibelle().' - '.$this->getLibelle();
      return $this->getParent()->getLibelle();
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
	if ($key != 'cepage_RB')
	  $sum += $cepage->getTotalCaveParticuliere();
      }
      return $sum;
    }
    
    private function getSumCepageFields($field) {
      $sum = 0;
      foreach ($this->filter('^cepage') as $key => $cepage) {
	if ($key != 'cepage_RB')
	  $sum += $cepage->get($field);
      }
      return $sum;
    }


    public function getTotalVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
      $sum = 0;
      foreach($this->getAcheteursFromCepage($type) as $acheteur) {
	$sum += $acheteur->quantite_vendue;
      }
      return $sum;
    }

    public function getTotalVolumeForMinQuantite() {
      return $this->getTotalVolume() - $this->getTotalVolumeAcheteurs('negoces');
    }

    private function getAcheteursFromCepage($type = 'mouts|negoces|cooperatives', $exclude_cepage = '') {
      $acheteurs = array();
      foreach ($this->filter('^cepage') as $key => $cepage) {
	if (!$cepage->getTotalVolume()) {
	  continue;
	}
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
            foreach ($this->filter('^cepage') as $key => $object) {
	      if ($key != 'cepage_RB') {
                $acheteurs = $object->getTotalAcheteursByCvi($field);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
		  if (!isset($this->_total_acheteurs_by_cvi[$field][$cvi])) {
		    $this->_total_acheteurs_by_cvi[$field][$cvi] = 0;
		  }
		  $this->_total_acheteurs_by_cvi[$field][$cvi] += $quantite_vendue;
                }
	      }
            }
        }
        return $this->_total_acheteurs_by_cvi[$field];
    }

    public function update() {
        parent::update();
        $this->remove('acheteurs');
	$this->add('acheteurs');
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
	  return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100),0);
        } else {
            return 0;
        }
    }
    public function removeVolumes() {
      $this->volume_revendique = null;
      $this->total_volume = null;
      $this->dplc = null;
      foreach($this->filter('^cepage_') as $cepage) {
	$cepage->removeVolumes();
      }
    }

    public function hasRendementAppellation() {
        return $this->getConfig()->hasRendementAppellation();
    }

    public function hasRendementCepage() {
      foreach($this->filter('cepage_') as $cepage) {
	if ($cepage->hasRendement())
	  return true;
      }
      return false;
    }

    public function getDPLCAppellation() {
        $volume_dplc = 0;
        if ($this->hasRendementAppellation()) {
            $volume = $this->getTotalVolume();
            $volume_max = $this->getVolumeMaxAppellation();
            if ($volume > $volume_max) {
                $volume_dplc = $volume - $volume_max;
            } else {
                $volume_dplc = 0;
            }
        }
        return $volume_dplc;
    }

    public function getVolumeRevendiqueAppellation() {
        $volume_revendique = 0;
        if ($this->hasRendement() && $this->hasRendementAppellation()) {
            $volume = $this->getTotalVolume();
            $volume_max = $this->getVolumeMaxAppellation();
            if ($volume > $volume_max) {
                $volume_revendique = $volume_max;
            } else {
                $volume_revendique = $volume;
            }
        }
        return $volume_revendique;
    }

    public function getVolumeMaxAppellation() {
      return ($this->getTotalSuperficie()/100) * $this->getRendementAppellation();
    }

    public function getRendementAppellation() {
      return $this->getConfig()->getRendementAppellation();
    }

    public function getDPLCFinal() {
      $dplc_total = $this->getTotalDPLC();
      $dplc_final = $dplc_total;
      if ($this->hasRendement() && $this->hasRendementAppellation()) {
          $dplc_appellation = $this->getDPLCAppellation();
          if ($dplc_total < $dplc_appellation) {
            $dplc_final = $dplc_appellation;
          }
      }
      return $dplc_final;
    }

    public function getVolumeRevendiqueFinal() {
      $volume_revendique_total = $this->getTotalVolumeRevendique();
      $volume_revendique_final = $volume_revendique_total;
      if ($this->hasRendementAppellation()) {
          $volume_revendique_appellation = $this->getVolumeRevendiqueAppellation();
          if ($volume_revendique_total > $volume_revendique_appellation) {
            $volume_revendique_final = $volume_revendique_appellation;
          }
      }
      return $volume_revendique_final;
    }

    public function getRendement() {
      return $this->getConfig()->getRendement();
    }

    public function hasRendement() {
      return ($this->hasRendementCepage() || $this->hasRendementAppellation());
    }

    public function isNonSaisie() {
      $cpt = 0;
      foreach($this->filter('cepage_') as $key => $cepage) {
	$cpt ++;
	if (!$cepage->isNonSaisie())
	  return false;
      }
      return ($cpt);
    }
}
