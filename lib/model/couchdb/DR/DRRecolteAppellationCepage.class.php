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
}
