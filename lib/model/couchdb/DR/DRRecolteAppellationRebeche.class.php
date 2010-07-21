<?php

class DRRecolteAppellationRebeche extends BaseDRRecolteAppellationRebeche {
    public function addDetail($detail) {
        return $this->add(0, $detail);
    }
}

