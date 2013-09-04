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

}