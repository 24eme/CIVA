<?php
/**
 * Model for VracCouleur
 *
 */

class VracCouleur extends BaseVracCouleur 
{
    
    public function getLieu() 
    {
        return $this->getParent();
    }

    public function getChildrenNode() 
    {
        return $this->getCepages();
    }

    public function getCepages() 
    {
        return $this->filter('^cepage');
    }

}