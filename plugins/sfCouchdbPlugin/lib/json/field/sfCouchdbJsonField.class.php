<?php

abstract class sfCouchdbJsonField {
    protected $value;
    protected $key;
    protected $name = null;
    protected $numeric_key = false;
    protected $_couchdb_document = null;
    protected $_field_hash = null;
    protected $_is_collection = false;
    protected $_is_new = false;
    protected $_is_modified = false;

    public function __construct($name, $value, $numeric_key = false, $couchdb_document = null, $hash = null) {
        $this->numeric_key = $numeric_key;
        $this->_is_collection = false;
	$this->_couchdb_document = $couchdb_document;
	$this->_field_hash = $hash;
        if (!$numeric_key) {
	  $this->key = sfInflector::underscore(sfInflector::camelize($name));
	  $this->name = $name;
        }
        //$this->_is_new = true;
        $this->setValue($value);
    }

    public function getKey() {
        return $this->key;
    }

    public function getName() {
        return $this->name;
    }
    
    public function setValue($value) {
        if ($this->isValid($value)) {
            if (!($value instanceof sfCouchdbJson) && $this->value !== $value) {
                $this->_is_modified = true;
            }
            $this->value = $value;
        }
	if ($value instanceof sfCouchdbJson) {
	  $value->setCouchdbDocumentAndHash($this->_couchdb_document, $this->_field_hash);
	}
        return $this->value;
    }

    public function isValid($value) {
        return is_null($value);
    }

    public function getValue() {
        return $this->value;
    }

    public function isNumericKey() {
        return $this->numeric_key;
    }

    public function isCollection() {
        return $this->_is_collection;
    }
    
    public function __toString() {
        return $this->value;
    }

    public function getData() {
        throw new sfCouchdbException("Not implemented");
    }

    public function isNewOrModified() {
        return ($this->_is_modified || $this->_is_new);
    }

    public function setIsNew($value) {
        $this->_is_new = $value;
    }

    public function setIsModified($value) {
        $this->_is_modified = $value;
    }

    public function getDataModified() {
        return ($this->_is_modified || $this->_is_new);
    }
}