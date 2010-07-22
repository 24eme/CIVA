<?php

abstract class sfCouchdbJsonField {
    protected $value;
    protected $key;
    protected $name = null;
    protected $numeric_key = false;

    public function __construct($name, $value, $numeric_key = false) {
        $this->numeric_key = $numeric_key;
        if (!$numeric_key) {
            $this->key = sfInflector::underscore(sfInflector::camelize($name));
            $this->name = $name;
        }
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
            $this->value = $value;
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
    
    public function __toString() {
        return $this->value;
    }

    public function getData() {
        throw new sfCouchdbException("Not implemented");
    }
}