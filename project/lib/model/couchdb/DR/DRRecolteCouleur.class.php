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

    public function getChildrenNode() {

        return $this->getCepages();
    }

    public function getCepages() {

        return $this->filter('^cepage');
    }

    public function canHaveUsagesIndustrielsSaisi() {

        return !$this->isUsagesIndustrielsSaisiCepage();
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

    public function getAutreCouleur() {
        $couleurs = $this->getParent()->getCouleurs();
        foreach($couleurs as $couleur) {
            if ($couleur->getKey() != $this->getKey()) {
                return $couleur;
            }
        }
        throw new sfException("Pas d'autre couleur");
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
            $this->dplc = $this->getDplc(true);
            $this->usages_industriels = $this->getUsagesIndustriels(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
        }
        $this->cleanNoeuds();
    }
    
    protected function cleanNoeuds() {
        foreach($this->getCepages() as $cepage) {
            if (count($cepage->detail) == 0) {
                $this->remove($cepage->getKey());
            }
        }
    }
}
