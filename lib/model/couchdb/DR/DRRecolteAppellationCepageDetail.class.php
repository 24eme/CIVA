<?php

class DRRecolteAppellationCepageDetail extends BaseDRRecolteAppellationCepageDetail {
    public function save($doc) {
        if ($this->_is_new) {
            return $doc->addRecolte($this);
        }
        return $this;
    }

}
