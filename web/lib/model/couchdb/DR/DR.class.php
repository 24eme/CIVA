<?php
class DR extends BaseDR {
    public function addRecolte($detail) {
        return $this->getRecolte()
             ->addAppellation($detail->getAppellation())
             ->getLieu()
             ->addCepage($detail->getCepage())
             ->getDetail()
             ->add(0, $detail);
    }

    public function addRebeche($detail) {
        return $this->getRecolte()
             ->addAppellation($detail->getAppellation())
             ->getLieu()
             ->addRebeche()
             ->add(0, $detail);
    }
}