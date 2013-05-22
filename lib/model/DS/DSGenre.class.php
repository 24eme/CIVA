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
        $appellations = $this->getAppellations();
        $appellations_config = $this->getConfig()->getAppellations();
        $appellations_sorted = array();

        foreach($appellations_config as $hash => $appellation_config) {
            $hash = preg_replace('/^\/recolte/','declaration',$hash);
            if($this->exist($appellation_config->getKey())) {
                $appellations_sorted[$hash] = $this->get($appellation_config->getKey());
            }
        }

        return $appellations_sorted;
    }
    
    public function getAppellation($a = null){
        
        if(!$a) return $this->getMentions();
        foreach ($this->getChildrenNode() as $key => $appellation) {
            if(preg_match('/^appellation_'.$a.'$/', $key)){
                return $appellation;
            }
        }
        return null;
    }
    
   public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getCertification()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
}