<?php
/**
 * Model for DSDeclaration
 *
 */

class DSDeclaration extends BaseDSDeclaration {

public function getChildrenNode() {
    return $this->certification;
    }
    
    public function getAppellations() {
        return $this->add('certification')->add('genre')->filter('^appellation_');
    }
    
    public function getAppellation($a) {
        return $this->add('certification')->add('genre')->getAppellation($a);
    }
    
    
}