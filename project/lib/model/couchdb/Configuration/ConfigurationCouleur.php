<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage');
    }

    public function getNoeuds() {

        return $this->getCepages();
    }

    public function hasRendement() {
      return ($this->hasRendementCouleur());
    }
}
