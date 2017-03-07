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
     * @return acCouchdbJson
     */
    public function getConfigAppellations() {
        return $this->getConfig()->filter('^appellation_');
    }

    public function cleanAllNodes() {
        $mentionsKey = array("mentionVT" => "mentionVT", "mentionSGN" => "mentionSGN");
        foreach($this->getChildrenNode() as $appellation) {
            foreach($appellation->getChildrenNode() as $mention) {
                if(isset($mentionsKey[$mention->getKey()]) && count($mention->getProduitsDetails())) {
                    unset($mentionsKey[$mention->getKey()]);
                }
            }
            if(!count($appellation->getProduitsDetails()) && $this->getDocument()->acheteurs->exist('certification/genre')){
                $this->getDocument()->acheteurs->certification->genre->remove($appellation->getKey());
            }
        }

        foreach($mentionsKey as $mentionKey) {
            if(!$this->getDocument()->acheteurs->exist('certification/genre')) {
                continue;
            }
            $this->getDocument()->acheteurs->certification->genre->remove($mentionKey);
        }

        parent::cleanAllNodes();
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

    /**
     *
     * @param array $params
     */
    protected function update($params = array()) {
        parent::update($params);
    }
}
