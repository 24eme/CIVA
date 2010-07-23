<?php
class DR extends BaseDR {
    public function addRecolte($detail) {
        return $this->getRecolte()
             ->addAppellation($detail->getAppellation())
             ->getLieu()
             ->addCepage($detail->getCepage())
             ->getDetail()
             ->add(null, $detail);
    }

    public function getRecolteDetail($appellation, $cepage, $numero) {
        return $this->getRecolte()
             ->getAppellation($appellation)
             ->getLieu()
             ->getCepage($cepage)
             ->getDetail()
             ->get($numero);
    }

    public function addRebeche($detail) {
        return $this->getRecolte()
             ->addAppellation($detail->getAppellation())
             ->getLieu()
             ->addRebeche()
             ->getDetail()
             ->add(null, $detail);
    }
}