<?php

class DRRecolte extends BaseDRRecolte {
    public function addAppellation($appellation) {
        return $this->add('appellation_'.$appellation);
    }
}
