<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

    protected $_total_acheteurs_by_cvi = array();

    public function getConfig() {
        return sfCouchdbManager::getClient('Configuration')->getConfiguration()->get($this->getHash());
    }

    public function getLibelle() {
        return $this->getConfig()->getLibelle();
    }

    public function getLieux() {
        return $this->filter('^lieu');
    }

    public function getTotalVolume() {
        $field = 'total_volume';
        if ($r = $this->_get($field)) {
            return $r;
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getTotalSuperficie() {
        $field = 'total_superficie';
        if ($r = $this->_get($field)) {
            return $r;
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getVolumeRevendique() {
        $field = 'volume_revendique';
        if ($r = $this->_get($field)) {
            return $r;
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }


    public function getDplc() {
        $field = 'dplc';
        if ($r = $this->_get($field)) {
            return $r;
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getTotalCaveParticuliere() {
        return $this->store('total_cave_particuliere', array($this, 'getSumLieuWithMethod'), array('getTotalCaveParticuliere'));
    }
    
    public function getTotalAcheteursByCvi($field) {
        if (!isset($this->_total_acheteurs_by_cvi[$field])) {
            $this->_total_acheteurs_by_cvi[$field] = array();
            foreach ($this->filter('^lieu') as $object) {
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

    public function getVolumeAcheteur($cvi, $type) {
        $key = "volume_acheteurs_".$cvi."_".$type;
        if (!isset($this->_storage[$key])) {
            $sum = 0;
            foreach ($this->getLieux() as $key => $lieu) {
                $sum += $lieu->getVolumeAcheteur($cvi, $type);
            }
            $this->_storage[$key] = array('volume' => $sum, 'ratio_superficie' => round($this->getTotalSuperficie() * $sum / $this->getTotalVolume(), 2));
        }
        return $this->_storage[$key];
    }

    public function removeVolumes() {
        $this->volume_revendique = null;
        $this->total_volume = null;
        $this->dplc = null;
        foreach ($this->filter('^lieu') as $lieu) {
            $lieu->removeVolumes();
        }
    }

    public function hasAllLieu() {
        $nb_lieu = $this->filter('^lieu')->count();
        $nb_lieu_config = $this->getConfig()->filter('^lieu')->count();
        return (!($nb_lieu < $nb_lieu_config));
    }

    public function getAppellation() {
      $v = $this->_get('appellation');
      if (!$v)
	$this->_set('appellation', $this->getConfig()->getAppellation());
      return $this->_get('appellation');
    }

    protected function getSumLieuFields($field) {
        $sum = 0;
        foreach ($this->getLieux() as $key => $lieu) {
            $sum += $lieu->get($field);
        }
        return $sum;
    }

    protected function getSumLieuWithMethod($method) {
        $sum = 0;
        foreach ($this->getLieux() as $key => $lieu) {
            $sum += $lieu->$method();
        }
        return $sum;
    }

    public function getLieuChoices() {
        $lieu_choices = array('' => '');
        foreach ($this->getConfig()->filter('^lieu[0-9]') as $key => $item) {
            if (!$this->exist($key)) {
                $lieu_choices[$key] = $item->getLibelle();
            }
        }
        asort($lieu_choices);
        return $lieu_choices;
    }
}
