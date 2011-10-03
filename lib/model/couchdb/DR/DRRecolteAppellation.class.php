<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

    public function getConfig() {
        return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    public function getLibelle() {
        return $this->store('libelle', array($this, 'getInternalLibelle'));
    }
    
    protected function getInternalLibelle() {
        return $this->getConfig()->getLibelle();
    }

    public function getLieux() {
        return $this->filter('^lieu');
    }

    public function getTotalVolume($force_calcul = false) {
        $field = 'total_volume';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getTotalSuperficie($force_calcul = false) {
        $field = 'total_superficie';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getVolumeRevendique($force_calcul = false) {
        $field = 'volume_revendique';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }


    public function getDplc($force_calcul = false) {
        $field = 'dplc';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function getTotalCaveParticuliere() {
        return $this->store('total_cave_particuliere', array($this, 'getSumLieuWithMethod'), array('getTotalCaveParticuliere'));
    }
    
    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach ($this->getLieux() as $lieu) {
                $acheteurs = $lieu->getVolumeAcheteurs($type);
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

    public function removeVolumes() {
        $this->total_superficie = null;
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

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
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
        foreach ($this->getConfig()->filter('^lieu.+') as $key => $item) {
            if (!$this->exist($key)) {
                $lieu_choices[$key] = $item->getLibelle();
            }
        }
        asort($lieu_choices);
        return $lieu_choices;
    }

    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            /*$this->volume_revendique = $this->getVolumeRevendique(true);
            $this->total_superficie = $this->getTotalSuperficie(true);*/
        }
    }
}
