<?php

class DRRecolteCertification extends BaseDRRecolteCertification {

    public function getChildrenNode() {

        return $this->getGenres();
    }

    public function getAppellations() {

       return $this->getChildrenNodeDeep();
    }

    public function getGenres(){

        return $this->filter('^genre');
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul );
    }

    public function getUsagesIndustrielsCalcule(){
        
        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
    }

    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod('dplc', array($this, 'getSumNoeudFields'), $force_calcul );
    }

    protected function update($params = array()) {
        parent::update($params);
    }

}
