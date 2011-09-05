<?php

class ConfigurationLieu extends BaseConfigurationLieu {
  public function getCouleurs() {
    return $this->filter('^couleur');
  }
  
  public function getCouleur() {
    if ($this->getNbCouleurs() >1) {
      throw new sfException('Pas getCouleur si plusieurs couleurs');
    }
    return $this->_get('couleur');
  }
  public function getNbCouleurs() {
    return count($this->getCouleurs());
  }

  public function getCepages() {
    return $this->getCouleur()->getCepages();
  }

  public function hasRendementCepage() {
    foreach($this->getCepages() as $cepage) {
      if ($cepage->hasRendement())
	return true;
    }
    return false;
  }
  
  public function hasRendement() {
    return ($this->hasRendementCepage() || $this->hasRendementAppellation());
  }
}
