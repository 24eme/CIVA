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

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getVolumeRevendiqueFinal'), $force_calcul);
    }

    public function getVolumeRevendiqueFinal() {

        return $this->getTotalVolume() - $this->getUsagesIndustriels();
    }

    public function getVolumeRevendiqueTotal() {

        return parent::getDataByFieldAndMethod('volume_revendique_total', array($this, 'getSumNoeudFields'), true, array('volume_revendique'));
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

        return parent::getDataByFieldAndMethod('dplc', array($this, 'getDplcFinal'), $force_calcul);
    }

    public function getDplcFinal() {
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

        return parent::getDataByFieldAndMethod('dplc_total', array($this, 'getSumNoeudFields'), true, array('dplc'));
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

    public function getUsagesIndustriels($force_calcul = false) {

        return parent::getDataByFieldAndMethod('usages_industriels', array($this, 'getUsagesIndustrielsFinal'), $force_calcul);
    }

    protected function getUsagesIndustrielsFinal() {
        if($this->haveUsagesIndustrielsSaisi()) {

          return $this->getSumNoeudFields('usages_industriels', false);
        }

        return $this->getDplc();
    }

    public function haveUsagesIndustrielsSaisi() {

        return $this->getLieu()->usages_industriels_noeud == DRRecolteLieu::USAGES_INDUSTRIELS_NOEUD_DETAIL;
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
