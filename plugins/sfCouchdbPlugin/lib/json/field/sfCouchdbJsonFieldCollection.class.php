<?php

class sfCouchdbJsonFieldCollection extends sfCouchdbJsonField {
    public function  __construct($name, $value, $numeric_key = false, $couchdb_document = null, $hash = null) {
        parent::__construct($name, $value, $numeric_key, $couchdb_document, $hash);
        $this->_is_collection = true;
    }
    public function getData() {
        return $this->value->getData();
    }
    public function isNewOrModified() {
        return $this->value->isDataModified();
    }

    public function getDataModified() {
        return $this->value->getDataModified();
    }
    public function isValid($value) {
        if (!($value instanceof sfCouchdbJson)) {
            throw new sfCouchdbException(sprintf('Not valid : %s', $this->key));
        }

        return true;
    }
}