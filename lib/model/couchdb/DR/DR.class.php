<?php
class DR extends BaseDR {
  public function addRecolte($detail) {
    $lieu = $detail->code_lieu;
    return $this->getRecolte()
      ->addAppellation($detail->getAppellation())
      ->addCepage($detail->getCepage(), $lieu)
      ->getDetail()
      ->add(null, $detail);
  }

    public function getRecolteDetail($appellation, $cepage, $numero, $lieu = '') {
        return $this->getRecolte()
             ->getAppellation($appellation)
	  ->getCepage($cepage, $lieu)
             ->getDetail()
             ->get($numero);
    }
    public function removeVolumes() {
      $this->lies = null;
      return $this->recolte->removeVolumes();
    }


    public function getTotalVolume() {
      $v = 0;
      foreach($this->recolte->filter('^appellation_') as $appellation) {
	$v += $appellation->getTotalVolume();
      }
      return $v;
    }
    public function getRatioLies() {
      if (!($v = $this->getTotalVolume())) {
	return 0;
      }
      return $this->lies / $v;
    }

    public function getLies(){
        $v = $this->_get('lies');
        if(!$v)
            return 0;
        else
            return $v;
    }

    public function update() {
      parent::update();
      $u = $this->add('updated', 1);
    }

    public function isValidee() {
      return $this->isValidated();
    }
    public function isValidated() {
      if ($this->exist('validee'))
	  return ($this->validee);
      return false;
    }

    public function getJeunesVignes(){
        $v = $this->_get('jeunes_vignes');
        if(!$v)
            return 0;
        else
            return $v;
    }
}