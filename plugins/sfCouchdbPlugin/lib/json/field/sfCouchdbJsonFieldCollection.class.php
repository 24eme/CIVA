<?php

class sfCouchdbJsonFieldCollection extends sfCouchdbJsonField {

    public function getData() {
        return $this->value->getData();
    }
    public function isValid($value) {
        if (!($value instanceof sfCouchdbJson)) {
            throw new sfCouchdbException(sprintf('Not valid : %s', $this->key));
        }

        return true;
    }
}