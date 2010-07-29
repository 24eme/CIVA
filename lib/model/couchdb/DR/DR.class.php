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

}