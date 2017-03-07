<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

    public function getChildrenNode() {
        return $this->getMentions();
    }

    public function getMentions(){
        return $this->filter('^mention');
    }

    public function getLieux() {
        $lieux = array();
        foreach($this->getMentions() as $mention) {
            foreach($mention->getChildrenNode() as $lieu) {

                $lieux[$lieu->getHash()] = $lieu;
            }
        }

        return $lieux;
    }

    public function getLibelleCourt() {
        $libelle = str_replace("AOC", "", $this->getLibelle());
        $libelle = str_replace("Alsace Communale", "Communale", $libelle);
        $libelle = str_replace("Alsace Grand Cru", "Grd Cru", $libelle);

        return $libelle;
    }

    public function hasDetailsInLieu($lieuKey) {
        foreach($this->getMentions() as $mention) {
            if($mention->getLieux()->exist($lieuKey) && count($mention->getLieux()->get($lieuKey)->getProduitsDetails())) {

                return true;
            }
        }

        return false;
    }

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod("volume_revendique", array($this,"getSumNoeudFields") , $force_calcul);
    }

    public function getDplc($force_calcul = false) {

        return parent::getDataByFieldAndMethod("dplc", array($this,"getSumNoeudFields") , $force_calcul);

    }

    public function hasCepageRB() {
        foreach($this->getLieux() as $lieu) {
            if($lieu->hasCepageRB()) {
                return true;
            }
        }

        return false;
    }


    public function hasAllDistinctLieu() {
        $nb_lieu = count($this->getDistinctLieux());
        $nb_lieu_config = count($this->getConfig()->getLieux());
        return (!($nb_lieu < $nb_lieu_config));
    }


    public function getUsagesIndustrielsCalcule(){

        return parent::getDataByFieldAndMethod("usages_industriels_calcule", array($this,"getSumNoeudFields") , true);
    }

    public function getAppellation() {
      $v = $this->_get('appellation');
      if (!$v)
	$this->_set('appellation', $this->getConfig()->getKey());
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
        foreach ($this->getConfig()->getLieux() as $item) {
                $hash = HashMapper::inverse($item->getHash());
                if(!$this->getDocument()->exist($hash)) {
                    $lieu_choices["lieu".$item->getKey()] = $item->getLibelle();
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
