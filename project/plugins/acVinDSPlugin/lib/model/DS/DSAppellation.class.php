<?php
/**
 * Model for DSAppellation
 *
 */

class DSAppellation extends BaseDSAppellation {
    
    public function getGenre(){
    
        return $this->getParent();
    }
    
    public function getChildrenNode() {
        return $this->getMentions();
    }

    public function getMentions(){

        return $this->filter('^mention');
    }

    public function getLieux() {  
        
        return $this->mention->getLieux();
    }

    public function getLieuxSorted() {  
        
        return $this->mention->getLieuxSorted();
    }

    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getGenre()->updateVolumes($vtsgn,$old_volume,$volume);
    }

    public function hasManyLieu() {

        return $this->getChildrenNodeDeep()->hasManyNoeuds();
    }

    public function isAutoCepages(){
        return $this->getConfig()->exist('auto_ds') && ($this->getConfig()->auto_ds == 1);
    }
}
