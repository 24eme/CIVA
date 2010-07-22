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

    public function addRebeche($detail) {
        return $this->getRecolte()
             ->addAppellation($detail->getAppellation())
             ->getLieu()
             ->addRebeche()
             ->add(null, $detail);
    }
}