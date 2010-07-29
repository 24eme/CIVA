<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {
  public function addCepage($cepage, $lieu = '') {
    return $this->add("lieu$lieu")->add('cepage_'.$cepage);
  }
  public function getCepage($cepage, $lieu = '') {
    return $this->get("lieu$lieu")->get('cepage_'.$cepage);
  }

    private function getSumCepageFields($field) {
      $sum = 0;
      foreach ($this->getData() as $key => $lieu) {
	if (preg_match("/^lieu/", $key)) {
	  foreach ($lieu as $key => $cepage) {
	    if (preg_match('/^cepage/', $key))
	      $sum += $cepage->{$field};
	  }
	}
      }
      return $sum;
    }

    public function getTotalVolume() {
      if ($r = parent::get('total_volume'))
	return $r;
      return $this->getSumCepageFields('total_volume');
      
    }
    public function getTotalSuperficie() {
      if ($r = parent::get('total_superficie'))
	return $r;
      return $this->getSumCepageFields('total_superficie');
    }
    public function getTotalDPLC() {
      return $this->getSumCepageFields('dplc');
    }
    public function getTotalVolumeRevendique() {
      return $this->getSumCepageFields('volume_revendique');
    }
}
