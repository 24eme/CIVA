<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage');
    }

    public function getChildrenNode() {

        return $this->getCepages();
    }

    public function hasRendement() {
      return ($this->hasRendementCouleur());
    }
}
