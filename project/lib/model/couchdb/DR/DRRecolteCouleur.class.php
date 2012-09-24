<?php

class DRRecolteCouleur extends BaseDRRecolteCouleur {

    public function getLibelleWithAppellation() {
      if ($this->getLibelle())
	return $this->getAppellation()->getLibelle().' - '.$this->getLibelle();
      return $this->getAppellation()->getLibelle();
    }

    public function getLieu() {
        return $this->getParent();
    }

    public function getAppellation() {

        return $this->getLieu()->getAppellation();
    }

    public function getNoeuds() {

        return $this->getCepages();
    }

    public function getCepages() {

        return $this->filter('^cepage');
    }

    public function getTotalVolume($force_calcul = false) {
        $field = 'total_volume';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumCepageFields'), array($field));
    }

    public function getTotalSuperficie($force_calcul = false) {
        $field = 'total_superficie';
        if (!$force_calcul && $this->issetField($field)) {
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

    protected function getVolumeRevendiqueFinal() {
        $volume_revendique_total = $this->getVolumeRevendiqueTotal();
        $volume_revendique_final = $volume_revendique_total;
        if ($this->getConfig()->hasRendementCouleur()) {
            $volume_revendique_couleur = $this->getVolumeRevendiqueCouleur();
            if ($volume_revendique_total > $volume_revendique_couleur) {
                $volume_revendique_final = $volume_revendique_couleur;
            }
        }
        return $volume_revendique_final;
    }

    public function getVolumeRevendiqueTotal() {
        return $this->store('volume_revendique_total', array($this, 'getSumCepageFields'), array('volume_revendique'));
    }

    public function getVolumeMaxCouleur() {
        return round(($this->getTotalSuperficie() / 100) * $this->getConfig()->getRendementCouleur(), 2);
    }

    public function getVolumeRevendiqueCouleur() {
        $key = "volume_revendique_couleur";
        if (!isset($this->_storage[$key])) {
            $volume_revendique = 0;
            if ($this->getConfig()->hasRendementCouleur()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxCouleur();
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

    public function getVolumeRevendiqueRendement() {
        
        return $this->getVolumeRevendiqueCouleur();
    }

    public function getDplc($force_calcul = false) {
        $field = 'dplc';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getDplcFinal'));
    }

    protected function getDplcFinal() {
        $dplc_total = $this->getDplcTotal();
        $dplc_final = $dplc_total;
        if ($this->getConfig()->hasRendement() && $this->getConfig()->hasRendementCouleur()) {
            $dplc_couleur = $this->getDplcCouleur();
            if ($dplc_total < $dplc_couleur) {
                $dplc_final = $dplc_couleur;
            }
        }
        return $dplc_final;
    }

    public function getDplcTotal() {
        return $this->store('dplc_total', array($this, 'getSumCepageFields'), array('dplc'));
    }

    public function getDplcRendement() {

        return $this->getDplcCouleur();
    }

    public function getDplcCouleur() {
        $key = "dplc_couleur";
        if (!isset($this->_storage[$key])) {
            $volume_dplc = 0;
            if ($this->getConfig()->hasRendementCouleur()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxCouleur();
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
        $key = "volume_acheteurs_" . $type;
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
        $key = "total_volume_acheteurs_" . $type;
        if (!isset($this->_storage[$key])) {
            $sum = 0;
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $volume) {
                $sum += $volume;
            }
            $this->_storage[$key] = $sum;
        }
        return $this->_storage[$key];
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
            return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100), 0);
        } else {
            return 0;
        }
    }

    public function getAutreCouleur() {
        $couleurs = $this->getParent()->getCouleurs();
        foreach($couleurs as $couleur) {
            if ($couleur->getKey() != $this->getKey()) {
                return $couleur;
            }
        }
        throw new sfException("Pas d'autre couleur");
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

    public function getCodeDouane($vtsgn = '') {
        if ($this->getLieu()->getConfig()->hasManyCouleur()) {
            return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
        } else {
            return $this->getLieu()->getCodeDouane($vtsgn);
        }
    }

    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
            $this->dplc = $this->getDplc(true);
        }
        $this->clean();
    }
    
    protected function clean() {
        foreach($this->getCepages() as $cepage) {
            if (count($cepage->detail) == 0) {
                $this->remove($cepage->getKey());
            }
        }
    }
}
