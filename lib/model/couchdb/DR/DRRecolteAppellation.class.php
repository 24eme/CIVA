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
      $r = $this->_get('dplc');
      if ($r) 
	return $r;
      return $this->getSumCepageFields('dplc');
    }
    public function getTotalVolumeRevendique() {
      $r = $this->_get('volume_revendique');
      if ($r) 
	return $r;
      return $this->getSumCepageFields('volume_revendique');
    }
    
    public function save() {
      return $this->getCouchdbDocument()->save();
    }

    public function getVolumeAcheteur($cvi, $type) {
      $sum = 0;
      foreach ($this->filter('^lieu') as $key => $lieu) {
	$sum += $lieu->getVolumeAcheteur($cvi, $type);
      }
      return array('volume' => $sum, 'ratio_superficie' => round($this->getTotalSuperficie() * $sum / $this->getTotalVolume(), 2));
    }
}
