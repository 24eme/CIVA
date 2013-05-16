<?php
/**
 * Model for DSMention
 *
 */

class DSMention extends BaseDSMention {
    
    public function getAppellation(){

        return $this->getParent();
    }

    public function getChildrenNode() {

        return $this->getLieux();
    }

    public function getLieux(){

        return $this->filter('^lieu');
    }
    
    public function getAppellationLibelle() {
        
        return $this->getAppellation()->appellation;
    }
    
    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getAppellation()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
}