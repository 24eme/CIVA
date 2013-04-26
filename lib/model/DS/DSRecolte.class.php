<?php
/**
 * Model for DSRecolte
 *
 */

class DSRecolte extends BaseDSRecolte {
    
    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getNoeudAppellations() {

        return $this->add('certification')->add('genre');
    }

    public function getAppellations() {

        return $this->getNoeudAppellations()->getAppellations();
    }


    public function getCertifications() {

        return $this->filter('^certification');
    }
}