<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {

  protected $_total_acheteurs_by_cvi = array();

  public function addCepage($cepage, $lieu = '') {
    return $this->add("lieu$lieu")->add('cepage_'.$cepage);
  }
  public function getCepage($cepage, $lieu = '') {
    return $this->get("lieu$lieu")->get('cepage_'.$cepage);
  }


    public function getTotalAcheteursByCvi($field) {
        if (!isset($this->_total_acheteurs_by_cvi[$field])) {
            $this->_total_acheteurs_by_cvi[$field] = array();
            foreach ($this->filter('^lieu') as $object) {
                $acheteurs = $object->getTotalAcheteursByCvi($field);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
                    if (!isset($this->_total_acheteurs_by_cvi[$field][$cvi])) {
                        $this->_total_acheteurs_by_cvi[$field][$cvi] = 0;
                    }
                    $this->_total_acheteurs_by_cvi[$field][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_total_acheteurs_by_cvi[$field];
    }

    public function getTotalVolume() {
      $r = $this->_get('total_volume');
      if ($r)
	return $r;
      return $this->getSumLieu('total_volume');
      
    }
    public function getTotalSuperficie() {
      $r =  $this->_get('total_superficie');
      if ($r)
	return $r;
      return $this->getSumLieu('total_superficie');
    }
    public function getTotalDPLC() {
      $r = $this->_get('dplc');
      if ($r) 
	return $r;
      return $this->getSumLieu('dplc');
    }
    public function getTotalVolumeRevendique() {
      $r = $this->_get('volume_revendique');
      if ($r) 
	return $r;
      return $this->getSumLieu('volume_revendique');
    }

    public function getTotalCaveParticuliere() {
        return 0;
    }
    
    private function getSumLieu($type) {
      $sum = 0;
      foreach ($this->filter('^lieu') as $key => $lieu) {
	$sum += $lieu->get($type);
      }
      return $sum;
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
