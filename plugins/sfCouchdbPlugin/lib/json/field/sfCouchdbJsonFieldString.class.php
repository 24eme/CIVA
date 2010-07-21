<?php

class sfCouchdbJsonFieldString extends sfCouchdbJsonField {
    public function isValid($value) {
        if(parent::isValid($value)) {
            return true;
        }

        if (!is_string($value)) {
            print_r($value);
            throw new sfCouchdbException(sprintf('Not valid : %s', $this->key));
        }

        return true;
    }
    public function getData() {
        return $this->value;
    }
}