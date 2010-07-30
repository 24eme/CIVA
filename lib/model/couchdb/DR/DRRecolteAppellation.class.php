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
      $r = $this->_get('total_volume');
      if ($r)
	return $r;
      return $this->getSumCepageFields('total_volume');
      
    }
    public function getTotalSuperficie() {
      $r =  $this->_get('total_superficie');
      if ($r)
	return $r;
      return $this->getSumCepageFields('total_superficie');
    }
    public function getTotalDPLC() {
      return $this->getSumCepageFields('dplc');
    }
    public function getTotalVolumeRevendique() {
      return $this->getSumCepageFields('volume_revendique');
    }
    public function getVolumeAcheteur($cvi, $type) {
      $sum = 0;
      foreach ($this->getData() as $key => $lieu) {
	if (preg_match("/^lieu/", $key)) {
	  foreach ($lieu as $key => $cepage) {
	    if (preg_match('/^cepage/', $key))
	      foreach ($cepage->detail as $d) {
		if (isset($d->{$type}))
		    foreach ($d->{$type} as $a) {
		      if ($a->cvi == $cvi)
			$sum += $a->quantite_vendue;
		    }
	      }
	  }
	}
      }
      return array('volume' => $sum, 'ratio_superficie' => round($this->getTotalSuperficie() * $sum / $this->getTotalVolume(), 2));
    }
}
