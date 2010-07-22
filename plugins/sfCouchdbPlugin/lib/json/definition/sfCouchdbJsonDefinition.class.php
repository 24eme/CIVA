<?php

class sfCouchdbJsonDefinition {

    private $_fields = null;
    private $_hash = null;
    private $_model = null;


    public function __construct($model, $hash) {
        $this->_fields = array();
        $this->_model = $model;
        $this->_hash = $hash;
    }

    public function getModel() {
        return $this->_model;
    }

    public function getHash() {
        return $this->_hash;
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

    public function getDefinitionByHash($hash) {
        $tab_hash = explode('/', $hash);
        if (count($tab_hash) > 1 && $tab_hash[1] != '') {
            $current_field = $tab_hash[1];
            unset($tab_hash[0], $tab_hash[1]);
            $new_hash = '';
            foreach($tab_hash as $item) {
                $new_hash .= '/'.$item;
            }
            return $this->get($current_field)->getDefinitionByHash($new_hash);
        } else {
            return $this;
        }
    }

    public function findHashByClassName($class_name) {
        foreach($this->_fields as $field) {
            if ($field instanceof sfCouchdbJsonDefinitionFieldCollection) {
                if ($field->getCollectionClass() == $class_name) {
                    return $field->getDefinition()->getHash();
                } else {
                    $result = $field->getDefinition()->findHashByClassName($class_name);
                    if (!is_null($result)) {
                        return $result;
                    }
                }
            }
        }

        return null;
    }

    public function getJsonField($key, $item, $numeric_key) {
        if (!$this->hasField($key)) {
             throw new sfCouchdbException(sprintf("Definition error : %s", $key));
        }

        if ($item instanceof sfCouchdbJson) {
            $data = $item->getData();
        } else {
            $data = $item;
        }
        
        $field = $this->get($key);
        
        if ($field->isMultiple())
            return $field->getJsonField($data, $numeric_key, $key);
        else
            return $field->getJsonField($data, $numeric_key);
    }
}