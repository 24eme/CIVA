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

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul);
    }

    public function getUsagesIndustrielsCalcule(){
        
        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
    }

    public function cleanAllNodes() {   
        $keys_to_delete = array();
        foreach($this->getChildrenNodeSorted() as $item) {
            $item->cleanAllNodes();

            if(!count($item->getProduitsDetails())){
                $this->getDocument()->acheteurs->certification->genre->remove($item->getKey());
                $this->remove($item->getKey());
            }
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
