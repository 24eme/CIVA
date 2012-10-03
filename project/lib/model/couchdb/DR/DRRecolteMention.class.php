<?php

class DRRecolteMention extends BaseDRRecolteMention {

    public function getAppellation(){

        return $this->getParent();
    }

    public function hasManyLieu() {
        if ( count($this->filter('^lieu')) > 1 )
            return true;
        return false;
    }

    public function getNoeuds() {

        return $this->getLieux();
    }

    public function getLieux(){

        return $this->filter('^lieu');
    }

    public function getTotalCaveParticuliere() {

        return parent::getDataByFieldAndMethod("total_cave_particuliere", array($this,"getSumNoeudWithMethod"), true, array('getTotalCaveParticuliere'));
    }

    public function hasAllLieu() {
        $nb_lieu = $this->filter('^lieu')->count();
        $nb_lieu_config = $this->getConfig()->filter('^lieu')->count();
        return (!($nb_lieu < $nb_lieu_config));
    }

    public function getTotalUsagesIndustriels($force_calcul = false){

        return $this->getDataByFieldAndMethod("total_usages_industriels", array($this,"getSumNoeudFields"), $force_calcul, array('usages_industriels_calcule'));
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
