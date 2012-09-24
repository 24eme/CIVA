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

    public function getTotalUsagesIndustriels($force_calcul = false){

        $field = 'usages_industriels_calcule';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumLieuFields'), array($field));
    }

    public function hasAllLieu() {
        $nb_lieu = $this->filter('^lieu')->count();
        $nb_lieu_config = $this->getConfig()->filter('^lieu')->count();
        return (!($nb_lieu < $nb_lieu_config));
    }

    protected function getSumLieuFields($field) {
        $sum = 0;
        foreach ($this->getLieux() as $key => $lieu) {
            $sum += $lieu->get($field);
        }
        return $sum;
    }

    protected function getSumLieuWithMethod($method) {
        $sum = 0;
        foreach ($this->getLieux() as $key => $lieu) {
            $sum += $lieu->$method();
        }
        return $sum;
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }


    public function getTotalCaveParticuliere() {
        return $this->store('total_cave_particuliere', array($this, 'getSumLieuWithMethod'), array('getTotalCaveParticuliere'));
    }


    /****/
    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
            $this->dplc = $this->getDplc(true);
        }
    }
}
