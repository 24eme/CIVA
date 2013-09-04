<?php
/**
 * Model for VracDeclaration
 *
 */

class VracDeclaration extends BaseVracDeclaration {
    
    public function getChildrenNode() 
    {
        return $this->getCertifications();
    }

    public function getCertifications() 
    {
        return $this->filter('^certification');
    }
    
    public function getAppellations() 
    {
        if(!$this->exist('certification')) return array();
        return $this->getChildrenNodeDeep(2)->getAppellations();
    }

    public function getAppellationsSorted() 
    {
        if(!$this->exist('certification')) return array();
        return $this->getChildrenNodeDeep(2)->getAppellationsSorted();
    }
    
    public function getProduitsDetailsSorted()
    {
    	$produits = $this->getProduitsDetails();
    	$result = array();
    	foreach ($produits as $hash => $values) {
    		$result[$values->position] = array($hash => $values);
    	}
    	ksort($result);
    	return $result;
    }

}