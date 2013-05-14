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

    public function getMentions() {
       return $this->filter('^mention');
    }

    public function getAppellations(){
        return $this->filter('^appellation');
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