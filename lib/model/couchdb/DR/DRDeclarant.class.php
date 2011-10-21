<?php

class DRDeclarant extends BaseDRDeclarant {
  private function getOrViaDocument($key) {
    if ($this->exist($key) && ($v = $this->_get($key)))
      return $v;
    $recoltant = $this->getCouchdbDocument()->getRecoltantObject();
    if ($recoltant) {
        return $recoltant->exploitant->get($key);
    } else {
        return null;
    }
  }
  public function getNom() {
    return $this->getOrViaDocument('nom');
  }
  public function getTelephone() {
    return $this->getOrViaDocument('telephone');
  }
  public function getEmail() {
    return $this->getOrViaDocument('email');
  }
}
