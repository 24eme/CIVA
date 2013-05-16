<?php
/**
 * Model for DSLieu
 *
 */

class DSLieu extends BaseDSLieu {
    
    public function getMention() {

        return $this->getParent();
    }

    public function getChildrenNode() {

        return $this->getCouleurs();
    }

    public function getCouleurs() {
        
        return $this->filter('^couleur');
    }
    
    public function getAppellationLibelle() {
        
        return $this->getMention()->getAppellation()->appellation;
    }
    
    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getMention()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
    public function getLieuLibelle() {
        return $this->getConfig()->getLibelle();
    }
    
}