<?php
/**
 * Model for DSGenre
 *
 */

class DSGenre extends BaseDSGenre {
    
    public function getCertification() {
        
        return $this->getParent();
    }

    public function getChildrenNode() {
        
        return $this->getAppellations();
    }

    public function getAppellations() {

        return $this->filter('^appellation');
    }
        
    public function getAppellationsSorted() {
        
        return $this->getChildrenNodeSorted();
    }
    
   public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getCertification()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
}