<?php

class DRRecolteCertification extends BaseDRRecolteCertification {

    public function getNoeuds() {

        return $this->getGenres();
    }

    public function getNoeud() {
        parent::getNoeud();
        return $this->genre;
    }

    public function getAppellations() {

       return $this->getNoeudsSuivant();
    }

    public function getGenres(){

        return $this->filter('^genre');
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul );
    }


    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod('dplc', array($this, 'getSumNoeudFields'), $force_calcul );
    }

    public function getTotalCaveParticuliere() {

        return parent::getDataByFieldAndMethod('total_cave_particuliere', array($this, 'getSumNoeudWithMethod'), true, array('getTotalCaveParticuliere') );
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

    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            $this->total_usages_industriels = $this->getTotalUsagesIndustriels(true);

        }
    }

}
