<?php

class sfCouchdbJsonFieldInteger extends sfCouchdbJsonField {
    public function isValid($value) {
        if(parent::isValid($value)) {
            return true;
        }

        if (!is_int($value)) {
            throw new sfCouchdbException(sprintf('Not valid : %s', $this->key));
        }

        return true;
    }
    public function getData() {
        return $this->value;
    }
}