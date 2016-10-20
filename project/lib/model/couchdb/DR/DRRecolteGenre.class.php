<?php

class DRRecolteGenre extends BaseDRRecolteGenre {

    public function getChildrenNode() {
        return $this->getAppellations();
    }

    public function getAppellations(){
        return $this->filter('^appellation');
    }

    public function getAppellationsSorted() {

        return $this->getChildrenNodeSorted();
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
        return (!($this->getAppellations()->count() < $this->getConfigAppellations()->getChildrenNode()->count()));
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul);
    }

    public function getUsagesIndustrielsCalcule(){

        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
    }

    public function cleanAllNodes() {
        $keys_to_delete = array();
        foreach($this->getChildrenNode() as $item) {
            $item->cleanAllNodes();

            if(!count($item->getProduitsDetails())){
                $this->getDocument()->acheteurs->certification->genre->remove($item->getKey());
                $keys_to_delete[$item->getKey()] = $item->getKey();
            }
        }

        foreach($keys_to_delete as $key) {
            $this->remove($key);
        }
    }

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);
    }
}
