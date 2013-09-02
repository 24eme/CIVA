<?php
/**
 * Model for VracAppellation
 *
 */

class VracAppellation extends BaseVracAppellation {
    
    public function getGenre()
    {
        return $this->getParent();
    }
    
    public function getChildrenNode() 
    {
        return $this->getMentions();
    }

    public function getMentions()
    {
        return $this->filter('^mention');
    }

    public function getLieux() 
    {  
        return $this->mention->getLieux();
    }

    public function getLieuxSorted() 
    {  
        return $this->mention->getLieuxSorted();
    }

}