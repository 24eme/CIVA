<?php
/**
 * Model for VracLieu
 *
 */

class VracLieu extends BaseVracLieu 
{
    
    public function getMention() 
    {
        return $this->getParent();
    }

    public function getAppellation() 
    {
        return $this->getMention()->getParent();
    }

    public function getChildrenNode() 
    {
        return $this->getCouleurs();
    }

    public function getCouleurs() 
    {
        return $this->filter('^couleur');
    }
    
    public function getAppellationLibelle() 
    {
        return $this->getMention()->getAppellation()->appellation;
    }

}