<?php

class DRRecolteAppellation extends BaseDRRecolteAppellation {
    public function addCepage($cepage) {
        return $this->add('cepage_'.$cepage);
    }
    public function addRebeche() {
        return $this->add('rebeche');
    }
}
