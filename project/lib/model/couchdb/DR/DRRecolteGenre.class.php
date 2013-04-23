<?php

class DRRecolteGenre extends BaseDRRecolteGenre {

    public function getChildrenNode() {
        return $this->getAppellations();
    }

    public function getMentions() {
       return $this->getChildrenNodeDeep();
    }

    public function getAppellations(){
        return $this->filter('^appellation');
    }

    /**
     *
     * @return boolean
     */
    public function hasOneOrMoreAppellation() {
        return $this->getAppellations()->count() > 0;
    }

    /**
     *
     * @return acCouchdbJson
     */
    public function getConfigAppellations() {
        return $this->getConfig()->filter('^appellation_');
    }

    /*
     * @return boolean
     */
    public function hasAllAppellation() {
        return (!($this->getAppellations()->count() < $this->getConfigAppellations()->count()));
    }

    /**
     *
     */
    public function removeVolumes() {
        foreach ($this->getAppellations() as $appellation) {
            $appellation->removeVolumes();
        }
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul);
    }

    public function getUsagesIndustrielsCalcule(){
        
        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
    }

    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod('dplc', array($this, 'getSumNoeudFields'), $force_calcul );
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

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);
    }
}
