<?php

class DRRecolte extends BaseDRRecolte {
    public function addAppellation($appellation) {
        return $this->add('appellation_'.$appellation);
    }
    public function getAppellation($appellation) {
        return $this->get('appellation_'.$appellation);
    }
}
