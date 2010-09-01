<?php

class Douane extends BaseDouane {
  private function getParentValue($key) {
    if ($this->getParent()->getKey() == 'recolte' || !$this->getParent()->getParent()->exist('douane'))
      throw new sfCouchdbException("$key not found");
    return $this->getParent()->getParent()->getDouane()->getValue($key);
  }
  private function getValue($key) {
    if ($this->exist($key) && ($v = $this->_get($key)) != null)
      return $v;
    return $this->getParentValue($key);
  }

  public function getTypeAoc() {
    return $this->getValue('type_aoc');
  }

  public function getCouleur() {
    return $this->getValue('couleur');
  }

  public function getAppellationLieu() {
    return $this->getValue('appellation_lieu');
  }

  public function getQualite($vtsgn = '') {
    switch($vtsgn) {
    case 'VT':
      return $this->getValue('qualite_vt');
    case 'SGN':
      return $this->getValue('qualite_sgn');
    case 'AOC':
      return $this->getValue('qualite_aoc');
    default:
      return $this->getValue('qualite');
    }
  }

  public function getCodeCepage() {
    return $this->getValue('code_cepage');
  }

  public function getFullAppCode($vtsgn = '') {
    return $this->getTypeAoc().$this->getCouleur().$this->getAppellationLieu().$this->getQualite($vtsgn);
  }
}
