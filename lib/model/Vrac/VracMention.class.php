<?php
/**
 * Model for VracMention
 *
 */

class VracMention extends BaseVracMention 
{
    
    public function getAppellation()
    {
        return $this->getParent();
    }

    public function getChildrenNode() 
    {
        return $this->getLieux();
    }

    public function getLieux()
    {
        return $this->filter('^lieu');
    }

    public function getLieuxSorted() 
    {
        return $this->getChildrenNodeSorted();
    }
    
    public function getAppellationLibelle() 
    {
        return $this->getAppellation()->appellation;
    }

}