<?php

class ConfigurationCouleur extends BaseConfigurationCouleur {
    public function getCepages() {
      return $this->filter('^cepage')->toArray();
    }

    public function hasRendementCepage() {
      foreach($this->getCepages() as $cepage) {
	if ($cepage->hasRendement())
	  return true;
      }
      return false;
    }

    public function getRendement() {
      return ;
    }

    public function hasRendement() {
      return ($this->hasRendementCepage() || $this->hasRendementAppellation());
    }
}
