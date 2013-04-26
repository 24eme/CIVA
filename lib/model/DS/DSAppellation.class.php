<?php
/**
 * Model for DSAppellation
 *
 */

class DSAppellation extends BaseDSAppellation {
    
    public function getChildrenNode() {
        return $this->getMentions();
    }

    public function getMentions(){
        return $this->filter('^mention');
    }

    public function getLieux() {
        
        return $this->getChildrenNodeDeep();
    }

}