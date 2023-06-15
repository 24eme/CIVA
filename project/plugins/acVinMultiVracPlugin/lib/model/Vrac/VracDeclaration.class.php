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
    
    public function getActifProduitsDetailsSorted()
    {
    	$produits = $this->getProduitsDetails();
    	$result = array();
    	foreach ($produits as $hash => $values) {
    		if ($values->actif) {
    			$result[$values->position] = array($hash => $values);
    		}
    	}
    	ksort($result);
    	return $result;
    }

    public function hasProduits()
    {
    	return (count($this->getActifProduitsDetailsSorted()) > 0)? true : false;
    }

    public function getProduitsWithVolumeBloque()
    {
        $result = [];
        foreach($this->getProduitsDetails() as $hash => $values) {
            if ($values->actif && $values->exist('dont_volume_bloque') && $values->dont_volume_bloque > 0) {
                $result[$hash] = $values;
            }
        }
        return $result;
    }

    public function hashProduitsWithVolumeBloque() {
        return (count($this->getProduitsWithVolumeBloque()) > 0);
    }

}
