<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

    public function getNoeuds() {

        return $this->getMentions();
    }

    public function getLieux() {

        if( $this->getConfig()->hasManyMention())
            throw new sfException("getLieux() ne peut être appelé d'une appellation qui a plusieurs mentions...");

        return $this->getMention()->filter('^lieu');
    }

    public function getMentions(){
        return $this->filter('^mention');
    }

/*
    public function getMention(){
        if ($this->getConfig()->hasManyMention())
            throw new sfException("getMention ne peut être appelé d'une appellation qui a plusieurs mentions...");

        return $this->_get('mention');
    }
*/
    public function getTotalVolume($force_calcul = false) {
        $field = 'total_volume';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumMentionFields'), array($field));
    }

    public function getTotalSuperficie($force_calcul = false) {
        $field = 'total_superficie';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumMentionFields'), array($field));
    }

    public function getVolumeRevendique($force_calcul = false) {
        $field = 'volume_revendique';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumMentionFields'), array($field));
    }


    public function getDplc($force_calcul = false) {
        $field = 'dplc';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumMentionFields'), array($field));
    }

    public function hasAllDistinctLieu() {
        $nb_lieu = count($this->getDistinctLieux());
        $nb_lieu_config = count($this->getConfig()->getDistinctLieux());
        return (!($nb_lieu < $nb_lieu_config));
    }

    public function getTotalCaveParticuliere() {
        return $this->store('total_cave_particuliere', array($this, 'getSumMentionWithMethod'), array('getTotalCaveParticuliere'));
    }
    /*** ???? ***/
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

    /*** ???? ***/
    public function getVolumeAcheteur($cvi, $type) {
        $volume = 0;
        $acheteurs = $this->getVolumeAcheteurs($type);
        if (array_key_exists($cvi, $acheteurs)) {
            $volume = $acheteurs[$cvi];
        }
        return $volume;
    }

    /*** ???? ***/
    public function removeVolumes() {
        $this->total_superficie = null;
        $this->volume_revendique = null;
        $this->total_volume = null;
        $this->dplc = null;
        foreach ($this->filter('^lieu') as $lieu) {
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

    public function getTotalUsagesIndustriels($force_calcul = false){

        $field = 'usages_industriels_calcule';
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }
        return $this->store($field, array($this, 'getSumMentionFields'), array($field));
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }

    protected function getSumMentionFields($field) {
        $sum = 0;
        foreach ($this->getMentions() as $key => $mention) {
            $sum += $mention->get($field);
        }
        return $sum;
    }

    protected function getSumMentionWithMethod($method) {
        $sum = 0;
        foreach ($this->getMentions() as $key => $mention) {
            $sum += $mention->$method();
        }
        return $sum;
    }

    protected function update($params = array()) {
        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            /*$this->volume_revendique = $this->getVolumeRevendique(true);
            $this->total_superficie = $this->getTotalSuperficie(true);*/
        }
    }



}
