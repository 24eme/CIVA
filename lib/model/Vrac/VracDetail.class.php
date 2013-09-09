<?php
/**
 * Model for VracDetail
 *
 */

class VracDetail extends BaseVracDetail {
	
	public function getCepage() {

        return $this->getParent()->getParent();
    }
	
	public function getLibelle() {

        return $this->getCepage()->getLibelle();
    }
    
    public function defineActive()
    {
    	if ($this->volume_propose && $this->prix_unitaire) {
    		$this->actif = 1;
    	} else {
    		$this->actif = 0;
    	}
    }

}