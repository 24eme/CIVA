<?php

class DRRecolteAppellationCepage extends BaseDRRecolteAppellationCepage {
    public function addDetail($detail) {
        return $this->add(null, $detail);
    }
}
