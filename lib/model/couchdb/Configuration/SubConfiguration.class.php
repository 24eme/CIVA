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

  public function hasDenomination() {
    if ($this->exist('no_denomination')) {
      return !($this->no_denomination == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
    } elseif ($this->getParent() instanceof SubConfiguration) {
      return $this->getParent()->hasTotalCepage();
    }
    return true;
  }

  public function hasSuperficie() {
    if ($this->exist('no_superficie')) {
      return !($this->no_superficie == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
    } elseif ($this->getParent() instanceof SubConfiguration) {
      return $this->getParent()->hasTotalCepage();
    }
    return true;
  }

  public function hasTotalCepage() {
    if ($this->exist('no_total_cepage')) {
      return !($this->no_total_cepage == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
    } elseif ($this->getParent() instanceof SubConfiguration) {
      return $this->getParent()->hasTotalCepage();
    }
    return true;
  }

  public function hasVtsgn() {
    if ($this->exist('no_vtsgn'))
      return (! $this->get('no_vtsgn'));
    if ($this->exist('min_quantite') && $this->get('min_quantite'))
      return false;
    return true;
  }

  public function isSuperficieRequired() {
    if ($this->exist('superficie_optionnelle'))
      return (! $this->get('superficie_optionnelle'));
    if ($this->exist('min_quantite') && $this->get('min_quantite'))
      return false;
    return true;
  }

  public function hasOnlyOneDetail() {
    if ($this->exist('only_one_detail') && $this->get('only_one_detail'))
      return true;
    if ($this->exist('min_quantite') && $this->get('min_quantite'))
      return true;
    return false;
  }
  public function hasMinQuantite() 
  {
    if ($this->exist('min_quantite') && $this->get('min_quantite'))
      return true;
    return false;
  }

  public function hasNoNegociant()
  {
    if ($this->exist('no_negociant') && $this->get('no_negociant'))
      return true;
    return false;
  }

  public function hasNoCooperative()
  {
    if ($this->exist('no_cooperative') && $this->get('no_cooperative'))
      return true;
    return false;
  }

  public function hasNoMout()
  {
    if ($this->exist('no_mout') && $this->get('no_mout'))
      return true;
    return false;
  }

  public function hasNoMotifNonRecolte()
  {
    if ($this->exist('no_motif_non_recolte') && $this->get('no_motif_non_recolte'))
      return true;
    return false;
  }

}
