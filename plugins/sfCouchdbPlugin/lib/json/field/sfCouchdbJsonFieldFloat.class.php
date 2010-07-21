<?php

class sfCouchdbJsonFieldFloat extends sfCouchdbJsonFieldInteger {

    public function isValid($value) {
        if (!is_float($value)) {
            return parent::isValid($value);
        }
        return true;
    }
}