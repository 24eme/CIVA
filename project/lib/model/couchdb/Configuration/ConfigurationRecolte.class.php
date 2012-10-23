<?php

class ConfigurationRecolte extends BaseConfigurationRecolte {

    public function getNoeudAppellations() {

        return $this->certification->genre;
    }
}