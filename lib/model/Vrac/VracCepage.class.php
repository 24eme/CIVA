<?php
/**
 * Model for VracCepage
 *
 */

class VracCepage extends BaseVracCepage 
{
    
    public function getCouleur() 
    {
        return $this->getParent();
    }

    public function getLieu() 
    {
        return $this->getCouleur()->getLieu();
    }

    public function getMention() 
    {
        return $this->getCouleur()->getLieu();
    }

    public function getAppellation() 
    {
        return $this->getLieu()->getAppellation();
    }

    public function getChildrenNode() 
    {
        return $this->detail;
    }

    public function getChildrenNodeSorted() 
    {
        return $this->getChildrenNode();
    }

    public function getProduits() 
    {
        return array($this->getHash() => $this);
    }
    
    public function getProduitsSorted() {
      
        return array($this->getHash() => $this);
    }

    public function hasVtsgn() 
    {
        return (bool) $this->no_vtsgn;
    }

    public function getProduitsDetails() 
    {
      $details = array();
      foreach($this->getChildrenNode() as $key => $item) {
          $details[$item->getHash()] = $item;
      } 
      return $details;
    }
	
    public function addDetail($config = null) 
    {
        $detail = $this->detail->add();
        if ($config) {
        	$detail->position = isset($config['position'])? (int)$config['position'] : null;
        	$detail->supprimable = isset($config['supprimable'])? (int)$config['supprimable'] : null;;
        }
        return $detail;
    }
    
    public function cleanAllNodes() {
        $details = $this->getProduitsDetails();
        foreach ($details as $detail) {
            if(!$detail->actif){
                $detail->delete();
            }
        }    
    }

}