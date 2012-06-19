<?php

class DRAcheteurs extends BaseDRAcheteurs {

  public function addAppellationTypeCVI($appellation, $type, $cvi) {
    $tab = $this->getArrayTypeWithAppellation($type);
    if (isset($tab[$cvi][$appellation]) && $tab[$cvi][$appellation])
      return true;
    return ($this->add($appellation)->add($type)->add(null,$cvi));
  }
  
  public function getArrayTypeWithAppellation($type) {
    $tab = array();
    foreach($this as $appellation => $acheteurs) {
      foreach($acheteurs->{$type} as $acheteur_cvi) {
	$tab[$acheteur_cvi][$appellation] = true;
      }
    }
    return $tab;
  }

  public function getArrayType($type) {
    return array_keys($this->getArrayTypeWithAppellation($type));
  }


    public function getArrayNegoces() {
      return $this->getArrayType('negoces');
    }

    public function getArrayNegocesWithAppellation() {
      return $this->getArrayTypeWithAppellation('negoces');
    }

    public function getArrayCooperatives() {
      return $this->getArrayType('cooperatives');
    }

    public function getArrayCooperativesWithAppellation() {
      return $this->getArrayTypeWithAppellation('cooperatives');
    }

    public function getArrayMouts() {
        $mouts = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[] = $acheteur_cvi;
            }
        }
        return $mouts;
    }

    public function getArrayMoutsWithAppellation() {
        $mouts = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[$acheteur_cvi][$appellation] = true;
            }
        }
        return $mouts;
    }

    public function getArrayCaveParticuliereWithAppellation() {
        $cave_particuliere = array();
        foreach($this as $appellation => $acheteurs) {
            $cave_particuliere[$appellation] = ($acheteurs->cave_particuliere == 1);
        }
        return $cave_particuliere;
    }
}
