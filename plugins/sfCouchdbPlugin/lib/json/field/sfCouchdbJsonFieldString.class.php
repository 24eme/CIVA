<?php

class sfCouchdbJsonFieldString extends sfCouchdbJsonField {
    public function isValid($value) {
        if(parent::isValid($value)) {
            return true;
        }

        return true;
    }
    public function getData() {
        return $this->value;
    }
}