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
