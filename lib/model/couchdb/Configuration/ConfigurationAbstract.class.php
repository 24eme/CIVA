<?php

abstract class ConfigurationAbstract extends sfCouchdbDocumentTree {

  public function getRendement() {
      return $this->store('rendement', array($this, 'getInternalRendement'));
  }
  
  protected function getInternalRendement() {
    if ($this->getParent()->exist('rendement') && $this->getParent()->_get('rendement') == -1) {
       return -1;
    }
    $r = $this->_get('rendement');
    if ($r) {
      return $r;
    }
    
    if ($this->getParent() instanceof ConfigurationAbstract) {
        return $this->getParent()->getRendement();
    } else {
        return 0;
    }
  }
  public function getRendementAppellation() {
    $r = null;
    if ($this->exist('rendement_appellation')) {
        $r = $this->_get('rendement_appellation');
    }
    if ($r) {
      return $r;
    }

    if ($this->getParent() instanceof ConfigurationAbstract) {
        return $this->getParent()->getRendementAppellation();
    } else {
        return 0;
    }
  }
  
  public function hasRendementAppellation() {
      $r = $this->getRendementAppellation();
      return ($r && $r > 0);
  }

  public function hasMout() {
      if ($this->exist('mout')) {
          return ($this->mout == 1);
      } elseif ($this->getParent() instanceof ConfigurationAbstract) {
          return $this->getParent()->hasMout();
      } else {
          return false;
      }
  }
  
  public function excludeTotal() 
  {
    return ($this->exist('exclude_total') && $this->get('exclude_total'));
  }

  protected function hasTotalCepage() {
    if ($this->exist('no_total_cepage')) {
      return !($this->no_total_cepage == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
    } elseif ($this->getParent() instanceof ConfigurationAbstract) {
      return $this->getParent()->hasTotalCepage();
    }
    return true;
  }
}
