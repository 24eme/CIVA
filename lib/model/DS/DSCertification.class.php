<?php
/**
 * Model for DSCertification
 *
 */

class DSCertification extends BaseDSCertification {
    
    public function getChildrenNode() {

        return $this->getGenres();
    }

    public function getAppellations() {

       return $this->getChildrenNodeDeep();
    }

    public function getGenres(){

        return $this->filter('^genre');
    }   

    
}