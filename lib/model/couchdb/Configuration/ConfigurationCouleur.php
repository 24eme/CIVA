<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage')->toArray();
    }

    public function hasRendement() {
      return ($this->hasRendementCouleur());
    }
}
