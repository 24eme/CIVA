<?php
/**
 * Model for DSDetail
 *
 */

class DSDetail extends BaseDSDetail {
    
    public function getProduitHash() {
        
        return $this->getParent()->getParent()->getHash();
    }
    
    public function getLibelle() {
        
        return $this->cepage;
    }
}