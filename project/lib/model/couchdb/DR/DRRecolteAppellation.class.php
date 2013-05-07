<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

    public function getChildrenNode() {
        return $this->getMentions();
    }

    public function getMentions(){
        return $this->filter('^mention');
    }

    public function getLieux() {
        
        return $this->getChildrenNodeDeep();
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod("volume_revendique", array($this,"getSumNoeudFields") , $force_calcul);
    }

    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod("dplc", array($this,"getSumNoeudFields") , $force_calcul);

    }

    public function hasAllDistinctLieu() {
        $nb_lieu = count($this->getDistinctLieux());
        $nb_lieu_config = count($this->getConfig()->getDistinctLieux());
        return (!($nb_lieu < $nb_lieu_config));
    }


    public function getUsagesIndustrielsCalcule(){
        
        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
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
        foreach ($this->getLieux() as $lieu) {
            $lieu->removeVolumes();
        }
    }

    public function getAppellation() {
      $v = $this->_get('appellation');
      if (!$v)
	$this->_set('appellation', $this->getConfig()->getAppellation());
      return $this->_get('appellation');
    }

    public function getDistinctLieux()
    {
        $arrLieux = array();
        foreach($this->getMentions() as $mention){
            foreach( $mention->getLieux() as $key =>  $lieu){
                if(!array_key_exists($key, $arrLieux)){
                    $arrLieux[$key] = $lieu;
                }
            }
        }
        return $arrLieux;
    }

    public function getLieuChoices() {
        $lieu_choices = array('' => '');
        foreach ($this->getConfig()->getDistinctLieux() as $key => $item) {
            foreach( $this->getMentions() as $mention ){
                if (!$mention->exist($key)) {
                    $lieu_choices[$key] = $item->getLibelle();
                }
            }
        }
        asort($lieu_choices);
        return $lieu_choices;
    }

    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
        }
    }

}
