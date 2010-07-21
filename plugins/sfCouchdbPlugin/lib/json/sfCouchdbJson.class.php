<?php

class sfCouchdbJson {

    private $_fields = null;
    protected $_definition = null;
    private $_is_array = false;

    public function __construct($definition = null) {
        $this->_fields = array();
        $this->_is_array = false;
        if (is_null($definition)) {
            $this->setupDefinition();
        } else {
            $this->_definition = $definition;
        }
        $this->initializeDefinition();
    }

    protected function setupDefinition() {
        $this->_definition = new sfCouchdbJsonDefinition(true);
    }

    private function initializeDefinition() {
        foreach($this->_definition->getRequiredFields() as $field_definition) {
            $this->add($field_definition->getKey(), null);
        }
    }

    public function setIsArray($value) {
       $this->_is_array = $value;
    }

    public function changeDefinition($definition) {
        $this->_definition = $definition;
        $this->initializeDefinition();
    }

    public function isArray() {
       return $this->_is_array;
    }

    public function load($data) {
        if (!is_null($data)) {
            foreach ($data as $key => $item) {
                $this->set($key, $item);
            }
        }
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get($key) {
        return $this->getField($key)->getValue();
    }

    public function set($key, $value = null) {
        $this->remove($key);
        return $this->add($key, $value);
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
        
        $field = $this->_definition->getJsonField($key, $item, $this->_is_array);

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