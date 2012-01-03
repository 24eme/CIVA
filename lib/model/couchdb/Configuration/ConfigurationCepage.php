<?php

class ConfigurationCepage extends BaseConfigurationCepage {

  public function hasRendement() {
      return ($this->getRendement()>0);
  }

  public function hasLieuEditable() {
        return $this->getParent()->getParent()->hasLieuEditable();
  }

  public function hasDenomination() {
    if ($this->exist('no_denomination')) {
      return !($this->no_denomination == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
    }
    return true;
  }

  public function hasSuperficie() {
    if ($this->exist('no_superficie')) {
      return !($this->no_superficie == 1);
    } elseif ($this->exist('min_quantite') && $this->get('min_quantite')) {
      return false;
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

  public function hasMaxQuantite()
  {
    if ($this->exist('max_quantite') && $this->get('max_quantite'))
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

  public function hasTotalCepage() {
    if (!$this->getRendement()) {
	return false;
    }

    return parent::hasTotalCepage();
  }
  
}