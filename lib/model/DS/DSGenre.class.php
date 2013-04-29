<?php
/**
 * Model for DSGenre
 *
 */

class DSGenre extends BaseDSGenre {
    
    public function getChildrenNode() {
        return $this->getAppellations();
    }

    public function getMentions() {
       return $this->getChildrenNodeDeep();
    }

    public function getAppellations(){
        return $this->filter('^appellation');
    }
    
}