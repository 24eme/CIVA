<?php
/**
 * Model for VracGenre
 *
 */

class VracGenre extends BaseVracGenre 
{
    
    public function getCertification() 
    {
        return $this->getParent();
    }

    public function getChildrenNode()
    {
        return $this->getAppellations();
    }

    public function getAppellations() 
    {
        return $this->filter('^appellation');
    }
        
    public function getAppellationsSorted() 
    {
        return $this->getChildrenNodeSorted();
    }

}