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

    public function getLieuxSorted() {
        $lieux = $this->getLieux();
        $lieux_config = $this->getConfig()->getLieux();
        $lieux_sorted = array();

        foreach($lieux_config as $hash => $lieu_config) {
            $hash = preg_replace('/^\/recolte/','declaration',$hash);
            if($this->exist($lieu_config->getKey())) {
                $lieux_sorted[$hash] = $this->get($lieu_config->getKey());
            }
        }

        return $lieux_sorted;
    }
    
    public function getAppellationLibelle() {
        
        return $this->getAppellation()->appellation;
    }
    
    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getAppellation()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
}