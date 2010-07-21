<?php

class sfCouchdbJsonFieldAnyone extends sfCouchdbJsonField {
    public function isValid($value) {
        return true;
    }
    public function getData() {
        return $this->value;
    }
}