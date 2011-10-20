<?php

class DRRecolteCepageDetail extends BaseDRRecolteCepageDetail {

    public function getConfig() {
        return $this->getCepage()->getConfig();
    }

    public function getCepageLibelle() {
      return $this->getCepage()->getLibelle();
    }

    public function getCepage() {
      return $this->getParent()->getParent();
    }

    public function getCodeDouane() {
        return $this->getCepage()->getCodeDouane($this->vtsgn);
    }

    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach ($this->filter($type) as $acheteurs) {
                foreach($acheteurs as $acheteur) {
                    $this->_storage[$key][$acheteur->cvi] = $acheteur->quantite_vendue;
                }
            }
        }
        return $this->_storage[$key];
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

    protected function deleteAcheteurUnused($type) {
        $appellation_key = $this->getCepage()->getLieu()->getAppellation()->getKey();
        if ($this->exist($type) && $this->getCouchdbDocument()->acheteurs->exist($appellation_key)) {
            $acheteurs = $this->getCouchdbDocument()->acheteurs->get($appellation_key)->get($type);
            $acheteurs_detail = $this->get($type);
            foreach ($acheteurs_detail as $key => $item) {
                if (!in_array($item->cvi, $acheteurs->toArray())) {
                    $acheteurs_detail->remove($key);
                }
            }
        }
    }

    public function getVolumeMax() {
        return round(($this->superficie / 100) * $this->getConfig()->getRendement(), 2);
    }

    private function getSumAcheteur($field) {
        $sum = 0;
        if ($this->exist($field)) {
            foreach ($this->get($field) as $acheteur) {
                $sum += $acheteur->quantite_vendue;
            }
        }
        return $sum;
    }

    public function removeVolumes() {
        $this->setVolume(null);
        $this->cave_particuliere = null;
        $this->remove('cooperatives');
        $this->remove('mouts');
        $this->remove('negoces');
    }

    public function hasMotifNonRecolteLibelle() {
        return $this->exist('motif_non_recolte');
    }

    public function isNonSaisie() {
        return ($this->getMotifNonRecolteLibelle() == 'Déclaration en cours');
    }

    public function getMotifNonRecolteLibelle() {
        if ($this->volume)
            return '';

        if ($this->exist('motif_non_recolte') && $this->getConfig()->getCouchdbDocument()->motif_non_recolte->exist($this->motif_non_recolte)) {
            return $this->getConfig()->getCouchdbDocument()->motif_non_recolte->get($this->motif_non_recolte);
        } else {
            return 'Déclaration en cours';
        }
    }
    
    public static function getUKey($denom, $vtsgn, $lieu = '') {
      return 'lieu:'.strtolower($lieu).',denomination:'.strtolower($denom).',vtsgn:'.strtolower($vtsgn);
    }

    public function getUniqueKey() {
      return self::getUKey($this->lieu, $this->denomination, $this->vtsgn);
    }

    protected function update($params = array()) {
        parent::update($params);
        if (!$this->getCouchdbDocument()->canUpdate())
            return;
        $v = $this->cave_particuliere;
        $v += $this->getSumAcheteur('negoces');
        $v += $this->getSumAcheteur('cooperatives');
        $v += $this->getSumAcheteur('mouts');

        $this->volume = $v;
        $this->volume_revendique = 0;
        $this->volume_dplc = 0;

        if ($this->getConfig()->hasRendement()) {
            $volume_max = $this->getVolumeMax();
            if ($this->volume > $volume_max) {
                $this->volume_revendique = $volume_max;
                $this->volume_dplc = $this->volume - $volume_max;
            } else {
                $this->volume_revendique = $this->volume;
            }
        } else {
            $this->volume_revendique = $this->volume;
        }

        if ($this->volume && $this->volume > 0) {
            $this->remove('motif_non_recolte');
        } else {
            $this->add('motif_non_recolte');
        }
        if (in_array('from_acheteurs', $params)) {
            $this->deleteAcheteurUnused('negoces');
            $this->deleteAcheteurUnused('cooperatives');
            $this->deleteAcheteurUnused('mouts');
        }
    }

}
