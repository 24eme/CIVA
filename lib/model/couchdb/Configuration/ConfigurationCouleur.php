<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage')->toArray();
    }
    
    public function getRendement() {
      return null;
    }

    public function hasRendement() {
      return ($this->hasRendementCouleur());
    }
}
