<?php

class DRRecolteAppellationCepage extends BaseDRRecolteAppellationCepage {
    public function addDetail($detail) {
      return $this->add(null, $detail);
    }
    protected function update() {
      parent::update();
      $s = 0;
      $v = 0;
      foreach ($this->get('detail') as $key => $item) {
	$v += $item->getVolume();
	$s += $item->getSuperficie();
      }

      $this->set('total_volume', $v);
      $this->set('total_superficie', $s);
    }
    private function getSumDetailFields($field) {
      $sum = 0;
      foreach ($this->getData()->detail as $detail) {
	$sum += $cepage->{$field};
      }
      return $sum;
    }
    public function getTotalVolume() {
      if ($r = parent::get('total_volume'))
	return $r;
      return $this->getSumDetailFields('total_volume');
      
    }
    public function getTotalSuperficie() {
      if ($r = parent::get('total_superficie'))
	return $r;
      return $this->getSumDetailFields('total_superficie');
    }

    public function getRendement() {
        return ConfigurationClient::getConfiguration()->get($this->getHash())->getRendement();
    }
}
