<?php

class sfCouchdbJsonFieldArrayCollection extends sfCouchdbJsonFieldCollection {
    public function isValid($value) {
        if (parent::isValid($value) && $value->isArray()) {
           return true;
        } else {
           throw new sfCouchdbException(sprintf('Not valid : %s', $this->key));
        }
    }
}