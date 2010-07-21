<?php

class sfCouchdbJsonDefinition {

    private $_fields = null;
    private $_free = false;

    public function __construct($free = false) {
        $this->_fields = array();
        $this->_free = $free;
    }

    public function setIsFree($value) {
        $this->_free = $value;
    }

    public function isFree() {
        return $this->_free;
    }

    public function add(sfCouchdbJsonDefinitionField $field) {
        if ($this->has($field->getKey())) {
            throw new sfCouchdbException(sprintf("This field already exist %s", $field->getKey()));
        }
        $this->_fields[$field->getKey()] = $field;

        return $field;
    }

    public function getRequiredFields() {

        $this->_required_fields = array();
        foreach($this->_fields as $key => $field) {
            if (!$field->isMultiple()) {
                $this->_required_fields[$key] = $field;
            }
        }
        return $this->_required_fields;
    }

    protected function has($key) {
        return isset($this->_fields[$key]);
    }

    protected function hasField($key) {
        if ($this->has($key)) {
            return true;
        }

        if ($this->has('*')) {
           return true;
        }

        return false;
    }

    protected function get($key) {
        if ($this->has($key)) {
            return $this->_fields[$key];
        }

        if ($this->has('*')) {
           return $this->_fields['*'];
        }
        
        throw new sfCouchdbException("This field doesn't exist");
    }

    public function findByClassName($class_name) {
        foreach($this->_fields as $field) {
            if ($field instanceof sfCouchdbJsonDefinitionFieldCollection) {
                if ($field->getCollectionClass() == $class_name) {
                    return $field->getDefinition();
                } else {
                    $result = $field->getDefinition()->findByClassName($class_name);
                    if (!is_null($result)) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    public function getJsonField($key, $item, $numeric_key) {
        if (!$this->isFree() && !$this->hasField($key)) {
             throw new sfCouchdbException(sprintf("Definition error : %s", $key));
        }

        if ($item instanceof sfCouchdbJson) {
            $data = $item->getData();
        } else {
            $data = $item;
        }
        if ($this->isFree()) {
            if ($data instanceof stdClass) {
                $field = new sfCouchdbJsonDefinitionFieldCollection($key);
            } elseif(is_array($data)) {
                $field = new sfCouchdbJsonDefinitionFieldArrayCollection($key);
            } else {
                $field = new sfCouchdbJsonDefinitionField($key, sfCouchdbJsonDefinitionField::TYPE_ANYONE);
            }
        } else {
            $field = $this->get($key);
        }
        if ($field->isMultiple())
            return $field->getJsonField($data, $numeric_key, $key);
        else
            return $field->getJsonField($data, $numeric_key);
    }
}