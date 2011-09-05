<?php

class DRRecolteLieu extends BaseDRRecolteLieu {

    public function getConfig() {
      return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    public function getLibelleWithAppellation() {
      if ($this->getLibelle())
	return $this->getParent()->getLibelle().' - '.$this->getLibelle();
      return $this->getParent()->getLibelle();
    }

    public function getLibelle() {
        return $this->store('libelle', array($this, 'getInternalLibelle'));
    }
    
    protected function getInternalLibelle() {
        return $this->getConfig()->getLibelle();
    }

    public function getAppellation() {
        return $this->getParent();
    }

    public function getCouleurs() {
      return $this->filter('^couleur');
    }

    public function getCouleur() {
      if ($this->getNbCouleurs() > 1) 
	throw new sfException("getCouleur() ne peut être appelé d'un lieu qui n'a qu'une seule couleur...");
      return $this->_get('couleur');
    }

    public function getNbCouleurs() {
      return count($this->filter('^couleur'));
    }

    public function getCepages() {
      return $this->getCouleur()->getCepages();
    }

    public function getCodeDouane($vtsgn = '') {
      if ($this->getParent()->getKey() == 'appellation_VINTABLE') {
	if ($this->getParent()->getParent()->filter('appellation_')->count() > 1) {
	  $vtsgn = 'AOC';
	}
      }
      if($this->getAppellation()->getConfig()->hasManyLieu()) {
          return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
      } else {
          return $this->getAppellation()->getConfig()->getDouane()->getFullAppCode($vtsgn);
      }
    }

    public function getTotalVolume() {
      $field = 'total_volume';
      if ($this->issetField($field)) {
        return $this->_get($field);
      }
      return $this->store($field, array($this, 'getSumCepageFields'), array($field));
    }
    public function getTotalSuperficie() {
      $field = 'total_superficie';
      if ($this->issetField($field)) {
        return $this->_get($field);
      }
      return $this->store($field, array($this, 'getSumCepageFields'), array($field));
    }

    public function getVolumeRevendique($force_calcul = false) {
      $field = 'volume_revendique';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
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

    public function getDplc($force_calcul = false) {
      $field = 'dplc';
      if (!$force_calcul && $this->issetField($field)) {
        return $this->_get($field);
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
            $this->_storage[$key] = round($volume_dplc, 2);
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
      return round($this->getTotalVolume() - $this->getTotalVolumeAcheteurs('negoces'), 2);
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
        $acheteurs = array();
        $types = array('negoces','cooperatives','mouts');
        foreach($types as $type) {
            foreach($this->getVolumeAcheteurs($type) as $cvi => $volume) {
                if (!isset($vol_total_cvi[$type.'_'.$cvi])) {
                    $vol_total_cvi[$type.'_'.$cvi] = 0;
                }
                $vol_total_cvi[$type.'_'.$cvi] += $volume;
            }
        }
        if (count($vol_total_cvi) != 1) {
            return false;
        }
        return true;
    }

    public function hasCompleteRecapitulatifVente() {
        if (!$this->getConfig()->hasRendement() || !$this->hasAcheteurs()) {
            return true;
        }

        foreach($this->acheteurs as $type => $type_acheteurs) {
            foreach($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->superficie) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTotalSuperficieRecapitulatifVente() {
        $total_superficie = 0;
        foreach($this->acheteurs as $type => $type_acheteurs) {
            foreach($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->superficie) {
                    $total_superficie += $acheteur->superficie;
                }
            }
        }

        return $total_superficie;
    }

    public function getTotalDontDplcRecapitulatifVente() {
        $total_dontdplc = 0;
        foreach($this->acheteurs as $type => $type_acheteurs) {
            foreach($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->dontdplc) {
                    $total_dontdplc += $acheteur->dontdplc;
                }
            }
        }

        return $total_dontdplc;
    }

    public function isValidRecapitulatifVente() {
        if (!$this->getConfig()->hasRendement()) {
            return true;
        }
        return (round($this->getTotalSuperficie(), 2) >= round($this->getTotalSuperficieRecapitulatifVente(), 2) &&
                round($this->getDplc(), 2) >= round($this->getTotalDontDplcRecapitulatifVente(), 2));
    }

    public function hasAcheteurs() {
       $nb_acheteurs = 0;
       foreach($this->acheteurs as $type => $type_acheteurs) {
           $nb_acheteurs += $type_acheteurs->count();
       }

       return $nb_acheteurs > 0;
    }

    public function getVolumeMaxAppellation() {
      return round(($this->getTotalSuperficie()/100) * $this->getConfig()->getRendementAppellation(), 2);
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

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
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
        $unique_acheteur = null;
        foreach($types as $type) {
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $cvi => $volume) {
                $acheteur = $this->acheteurs->add($type)->add($cvi);
                $acheteur->type_acheteur = $type;
                $unique_acheteur = $acheteur;
            }
            foreach($this->acheteurs->get($type) as $cvi => $item) {
                if (!array_key_exists($cvi, $acheteurs)) {
                    $this->acheteurs->get($type)->remove($cvi);
                }
            }
        }
        if ($this->getConfig()->hasRendement() && $this->hasSellToUniqueAcheteur()) {
            $unique_acheteur->superficie = $this->getTotalSuperficie();
            $unique_acheteur->dontdplc = $this->getDplc();
        }
        if ($this->getCouchdbDocument()->canUpdate()) {
            /*$this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);*/
            $this->volume_revendique = $this->getVolumeRevendique(true);
            $this->dplc = $this->getDplc(true);
        }
    }
}
