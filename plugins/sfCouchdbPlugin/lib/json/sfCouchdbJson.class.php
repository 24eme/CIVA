<?php

class sfCouchdbJson {

    private $_fields = null;
    private $_is_array = false;
    protected $_definition_hash = null;
    protected $_definition_model = null;

    public function __construct($definition_model = null, $definition_hash = null) {
        $this->_fields = array();
        $this->_is_array = false;
        if (is_null($definition_model) || is_null($definition_hash)) {
            $this->setupDefinition();
        } else {
            $this->_definition_model = $definition_model;
            $this->_definition_hash = $definition_hash;
        }
        $this->initializeDefinition();
    }

    protected function setupDefinition() {
        throw new sfCouchdbException('Definition not found');
    }

    private function initializeDefinition() {
        foreach($this->getDefinition()->getRequiredFields() as $field_definition) {
            $this->add($field_definition->getKey(), null);
        }
    }

    public function setIsArray($value) {
       $this->_is_array = $value;
    }

    public function getDefinition() {
        return sfCouchdbManager::getDefinitionByHash($this->_definition_model, $this->_definition_hash);
    }

    public function isArray() {
       return $this->_is_array;
    }

    public function load($data) {
        if (!is_null($data)) {
            foreach ($data as $key => $item) {
                $this->add($key, $item);
            }
        }
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get($key) {
        return $this->getField($key)->getValue();
    }

    public function set($key, $value) {
        return $this->getField($key)->setValue($value);
    }

    protected function remove($key) {
        if ($this->hasField($key)) {
            unset($this->_fields[$key]);
            return true;
        } else {
            return false;
        }
    }

    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    public function add($key = null, $item = null) {
        if ($this->hasField($key)) {
            return $this->get($key);
        }
        
        $field = $this->getDefinition()->getJsonField($key, $item, $this->_is_array);

        if ($field->isNumericKey()) {
            $this->_fields[] = $field;
        } else {
            $this->_fields[$field->getKey()] = $field;
        }

        return $field->getValue();
    }

    public function hasField($key) {
        return isset($this->_fields[sfInflector::underscore(sfInflector::camelize($key))]);
    }

    public function getField($key) {
        if ($this->hasField($key)) {
            return $this->_fields[sfInflector::underscore(sfInflector::camelize($key))];
        } else {
            throw new sfCouchdbException(sprintf('field inexistant : %s', $key));
        }
    }

    public function __call($method, $arguments) {
        if (in_array($verb = substr($method, 0, 3), array('set', 'get'))) {
            $name = sfInflector::underscore(sfInflector::camelize(substr($method, 3)));
            if ($this->hasField($name)) {
                return call_user_func_array(
                  array($this, $verb),
                  array_merge(array($name), $arguments)
                );
            } else {
                throw new sfCouchdbException(sprintf('Method undefined : %s', $method));
            }
        } else {
            throw new sfCouchdbException(sprintf('Method undefined : %s', $method));
        }
    }

    public function getData() {

        $data = array();
        
        foreach($this->_fields as $field) {
            if ($this->_is_array) {
                $data[] = $field->getData();
            } else {
                $data[$field->getName()] = $field->getData();
            }
        }
        
        if ($this->_is_array) {
            return $data;
        } else {
            return (Object) $data;
        }


        
    }

}