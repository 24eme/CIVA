<?php

class DRRecolteLieu extends BaseDRRecolteLieu {

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function getLibelleWithAppellation() {
      if ($this->getLibelle())
	return $this->getParent()->getLibelle().' - '.$this->getLibelle();
      return $this->getParent()->getLibelle();
    }

    public function getLibelle() {
        return ConfigurationClient::getConfiguration()->get($this->getHash())->getLibelle();
    }

    public function getAppellation() {
        return $this->getParent();
    }

    public function getCepages() {
        return $this->filter('^cepage');
    }

    public function getCodeDouane($vtsgn = '') {
      if ($this->getParent()->getKey() == 'appellation_VINTABLE') {
	if ($this->getParent()->getParent()->filter('appellation_')->count() > 1) {
	  $vtsgn = 'AOC';
	}
      }
      return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
    }

    public function getTotalVolume() {
      $field = 'total_volume';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumCepageFields'), array($field));
    }
    public function getTotalSuperficie() {
      $field = 'total_superficie';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getSumCepageFields'), array($field));
    }

    public function getVolumeRevendique() {
      $field = 'volume_revendique';
      if ($r = $this->_get($field)) {
        return $r;
      }
      return $this->store($field, array($this, 'getVolumeRevendiqueFinal'));
    }

    public function getVolumeRevendiqueTotal() {
        return $this->store('volume_revendique_total', array($this, 'getSumCepageFields'), array('volume_revendique'));
    }

    public function getVolumeRevendiqueAppellation() {
        $key = "volume_revendique_appellation";
        if (!isset($this->_storage[$key])) {
            $volume_revendique = 0;
            if ($this->getConfig()->hasRendement() && $this->getConfig()->hasRendementAppellation()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxAppellation();
                if ($volume > $volume_max) {
                    $volume_revendique = $volume_max;
                } else {
                    $volume_revendique = $volume;
                }
            }
            $this->_storage[$key] = $volume_revendique;
        }
        return $this->_storage[$key];
    }

    public function getDplc() {
      $field = 'dplc';
      if ($r = $this->_get($field)) {
        return $r;
      }
       return $this->store($field, array($this, 'getDplcFinal'));
    }

    public function getDplcTotal() {
       return $this->store('dplc_total', array($this, 'getSumCepageFields'), array('dplc'));
    }

    public function getDplcAppellation() {
        $key = "dplc_appellation";
        if (!isset($this->_storage[$key])) {
            $volume_dplc = 0;
            if ($this->getConfig()->hasRendementAppellation()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxAppellation();
                if ($volume > $volume_max) {
                    $volume_dplc = $volume - $volume_max;
                } else {
                    $volume_dplc = 0;
                }
            }
            $this->_storage[$key] = $volume_dplc;
        }
        return $this->_storage[$key];
    }

    public function getTotalCaveParticuliere() {
      $key = "total_cave_particuliere";
      if (!isset($this->_storage[$key])) {
          $sum = 0;
          foreach ($this->getCepages() as $key => $cepage) {
             if ($cepage->getConfig()->excludeTotal()) {
                      continue;
             }
             $sum += $cepage->getTotalCaveParticuliere();
          }
          $this->_storage[$key] = $sum;
      }
      return $this->_storage[$key];
    }
    
    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach ($this->getCepages() as $cepage) {
                if ($cepage->getConfig()->excludeTotal()) {
                      continue;
                }
                $acheteurs = $cepage->getVolumeAcheteurs($type);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
                  if (!isset($this->_storage[$key][$cvi])) {
                    $this->_storage[$key][$cvi] = 0;
                  }
                  $this->_storage[$key][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_storage[$key];
    }

     public function getVolumeAcheteur($cvi, $type) {
        $volume = 0;
        $acheteurs = $this->getVolumeAcheteurs($type);
        if (array_key_exists($cvi, $acheteurs)) {
            $volume = $acheteurs[$cvi];
        }
        return $volume;
    }

    public function getTotalVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "total_volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
              $sum = 0;
              $acheteurs = $this->getVolumeAcheteurs($type);
              foreach($acheteurs as $volume) {
                $sum += $volume;
              }
              $this->_storage[$key] = $sum;
        }
        return $this->_storage[$key];
    }

    public function getTotalVolumeForMinQuantite() {
      return $this->getTotalVolume() - $this->getTotalVolumeAcheteurs('negoces');
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
      foreach($this->getCepages() as $cepage) {
	$cepage->removeVolumes();
      }
    }

    public function hasSellToUniqueAcheteur() {
        if ($this->getTotalCaveParticuliere() > 0) {
            return false;
        }
        $vol_total_cvi = array();
        foreach($this->getVolumeAcheteurs() as $cvi => $volume) {
            if (!isset($vol_total_cvi[$cvi])) {
                $vol_total_cvi[$cvi] = 0;
            }
            $vol_total_cvi[$cvi] += $volume;
        }
        if (count($vol_total_cvi) != 1) {
            return false;
        }
        return true;
    }

    public function getVolumeMaxAppellation() {
      return ($this->getTotalSuperficie()/100) * $this->getConfig()->getRendementAppellation();
    }

    public function isNonSaisie() {
      $cpt = 0;
      foreach($this->getCepages() as $key => $cepage) {
	$cpt ++;
	if (!$cepage->isNonSaisie())
	  return false;
      }
      return ($cpt);
    }

    protected function getVolumeRevendiqueFinal() {
      $volume_revendique_total = $this->getVolumeRevendiqueTotal();
      $volume_revendique_final = $volume_revendique_total;
      if ($this->getConfig()->hasRendementAppellation()) {
          $volume_revendique_appellation = $this->getVolumeRevendiqueAppellation();
          if ($volume_revendique_total > $volume_revendique_appellation) {
            $volume_revendique_final = $volume_revendique_appellation;
          }
      }
      return $volume_revendique_final;
    }

    protected function getDplcFinal() {
      $dplc_total = $this->getDplcTotal();
      $dplc_final = $dplc_total;
      if ($this->getConfig()->hasRendement() && $this->getConfig()->hasRendementAppellation()) {
          $dplc_appellation = $this->getDplcAppellation();
          if ($dplc_total < $dplc_appellation) {
            $dplc_final = $dplc_appellation;
          }
      }
      return $dplc_final;
    }

    protected function getSumCepageFields($field) {
      $sum = 0;
      foreach ($this->getCepages() as $key => $cepage) {
	if ($key != 'cepage_RB')
	  $sum += $cepage->get($field);
      }
      return $sum;
    }

    protected function update($params = array()) {
        parent::update($params);
	$this->add('acheteurs');
        $types = array('negoces','cooperatives','mouts');
        foreach($types as $type) {
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $cvi => $volume) {
                $acheteur = $this->acheteurs->add($cvi);
                $acheteur->type_acheteur = $type;
            }
        }
        $acheteurs = $this->getVolumeAcheteurs();
        
        foreach($this->acheteurs as $cvi => $item) {
            if (!array_key_exists($cvi, $acheteurs)) {
                $this->acheteurs->remove($cvi);
            }
        }
    }
}
