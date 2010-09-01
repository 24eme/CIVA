<?php

class DRDeclarant extends BaseDRDeclarant {
  private function getOrViaDocument($key) {
    if ($this->exist($key) && ($v = $this->_get($key)))
      return $v;
    return sfCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->getCouchdbDocument()->cvi)->exploitant->{$key};
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
