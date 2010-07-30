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
}
