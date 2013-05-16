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
    
    public function getAppellations() {
      $appellations = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $appellations[$item->getHash()] = $item;
      }

      return $appellations;
    }
    
    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getGenre()->updateVolumes($vtsgn,$old_volume,$volume);
    }
    
    public function hasManyLieux() {
        return (bool) count($this->getLieux()->getLieux()) > 1;
    }
    

}