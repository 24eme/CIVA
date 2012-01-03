<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage');
    }

    public function hasRendement() {
      return ($this->hasRendementCouleur());
    }
}
