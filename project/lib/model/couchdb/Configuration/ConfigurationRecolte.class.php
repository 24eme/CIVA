<?php

class ConfigurationRecolte extends BaseConfigurationRecolte {

    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getCertifications() {

        return $this->filter('^certification');
    }

    public function getNoeudAppellations() {

        return $this->getChildrenNodeDeep(2);
    }

    public function hasNoUsagesIndustriels() {
        
        return ($this->exist('no_usages_industriels') && $this->get('no_usages_industriels'));
    }

    public function getRendementAppellation() {

        return 0;
    }

    public function getRendementCouleur() {

        return 0;
    }

    public function getRendement() {

        return 0;
    }

    public function hasMout() {

        return false;
    }

    public function hasTotalCepage() {

        return true;
    }

    public function hasVtsgn() {

        return true;
    }
}