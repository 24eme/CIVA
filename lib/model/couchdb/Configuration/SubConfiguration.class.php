<?php

class SubConfiguration extends BaseSubConfiguration {
  public function getRendement() {
    if ($this->getParent()->exist('rendement') && $this->getParent()->_get('rendement') == -1) {
       return -1;
    }

    $r = $this->_get('Rendement');
    if ($r && ($r > 0 || $r == -1)) {
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

  public function hasMout() {
      if ($this->exist('mout')) {
          return ($this->mout == 1);
      } elseif ($this->getParent() instanceof SubConfiguration) {
          return $this->getParent()->hasMout();
      } else {
          return false;
      }
  }

  public function hasOnlyOneDetail() {
    if ($this->exist('only_one_detail') && $this->get('only_one_detail'))
      return true;
    if ($this->exist('min_quantite') && $this->get('min_quantite'))
      return true;
    return false;
  }
}
