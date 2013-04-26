<?php
/**
 * Model for DSCouleur
 *
 */

class DSCouleur extends BaseDSCouleur {
    
    public function getLieu() {

        return $this->getParent();
    }

    public function getChildrenNode() {

        return $this->getCepages();
    }

    public function getCepages() {

        return $this->filter('^cepage');
    }
    
}