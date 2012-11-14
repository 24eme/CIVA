<?php

class ConfigurationRecolte extends BaseConfigurationRecolte {

    public function getNoeudAppellations() {

        return $this->certification->genre;
    }

    public function hasNoUsagesIndustriels() {
        
        return ($this->exist('no_usages_industriels') && $this->get('no_usages_industriels'));
    }
}