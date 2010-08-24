<?php

class SubConfiguration extends BaseSubConfiguration {
  public function getRendement() {
    $r = $this->_get('Rendement');
    if ($r && $r > 0) {
      return $r;
    }
    $h = $this->getParentHash();
    if ($h == '/recolte')
      return 0;
    return $this->getCouchdbDocument()->get($h)->getRendement();
  }

  public function getRendementAppellation() {
    $r = null;
    if ($this->exist('rendement_appellation')) {
        $r = $this->_get('rendement_appellation');
    }
    if ($r && $r > 0) {
      return $r;
    }
    $h = $this->getParentHash();
    if ($h == '/recolte')
      return 0;
    return $this->getCouchdbDocument()->get($h)->getRendementAppellation();
  }

  public function hasRendementAppellation() {
      $r = $this->getRendementAppellation();
      return ($r && $r > 0);
  }
}
