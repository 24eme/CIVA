<?php
/**
 * Model for DSCertification
 *
 */

class DSCertification extends BaseDSCertification {
    
    public function getChildrenNode() {

        return $this->getGenres();
    }

    public function getGenres(){

        return $this->filter('^genre');
    }   

    
}