<?php

class SubConfiguration extends BaseSubConfiguration {
  public function getRendement() {
    if ($r = parent::get('Rendement') && $r > 0)
      return $r;
    $h = $this->getParentHash();
    if ($h == 'Recolte')
      return 0;
    return $this->getCouchdbDocument()->get($h)->getRendement();
  }
}
