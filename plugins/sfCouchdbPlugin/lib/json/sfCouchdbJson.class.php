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
                $this->remove($key);
                $this->add($key, $item);
            }
        }
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get($key_or_hash) {
        $obj_hash = new sfCouchdbHash($key_or_hash);
        if ($obj_hash->isAlone()) {
           return $this->getField($obj_hash->getFirst())->getValue();
        } else {
           return $this->get($obj_hash->getFirst())->get($obj_hash->getAllWithoutFirst());
        }
    }

    public function set($key_or_hash, $value) {
        $obj_hash = new sfCouchdbHash($key_or_hash);
        if ($obj_hash->isAlone()) {
	  return $this->getField($obj_hash->getFirst())->setValue($value);
        } else {
	  return $this->get($obj_hash->getFirst())->set($obj_hash->getAllWithoutFirst(), $value);
        }
    }

    public function remove($key_or_hash) {
      $obj_hash = new sfCouchdbHash($key_or_hash);
      if ($obj_hash->isAlone()) {
	$key = $key_or_hash;
        if ($this->hasField($key)) {
            unset($this->_fields[$key]);
            return true;
        } 
	return false;
      }
      return $this->get($obj_hash->getFirst())->remove($obj_hash->getAllWithoutFirst());
    }

    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    public function add($key = null, $item = null) {
        if ($this->_is_array) {
            return $this->addNumeric($item);
        } else {
            return $this->addNormal($key, $item);
        }
    }

    protected function addNormal($key = null, $item = null) {
        if ($this->hasField($key)) {
            return $this->get($key);
        }

        $field = $this->getDefinition()->getJsonField($key, $item, false);
        $this->_fields[$field->getKey()] = $field;

        return $field->getValue();
    }

    protected function addNumeric($item = null) {
        $field = $this->getDefinition()->getJsonField(null, $item, true);
        $this->_fields[] = $field;

        return $field->getValue();
    }

    public function hasField($key) {
        if ($this->_is_array) {
            return $this->hasFieldNumeric($key);
        } else {
            return $this->hasFieldNormal($key);
        }
    }

    public function hasFieldNormal($key) {
        return isset($this->_fields[sfInflector::underscore(sfInflector::camelize($key))]);
    }

    public function hasFieldNumeric($key) {
        return isset($this->_fields[$key]);
    }

    public function getField($key) {
        if ($this->_is_array) {
            return $this->getFieldNumeric($key);
        } else {
            return $this->getFieldNormal($key);
        }
    }

    protected function getFieldNormal($key) {
         if ($this->hasField($key)) {
            return $this->_fields[sfInflector::underscore(sfInflector::camelize($key))];
        } else {
            throw new sfCouchdbException(sprintf('field inexistant : %s', $key));
        }
    }

    protected function getFieldNumeric($key) {
         if ($this->hasField($key)) {
            return $this->_fields[$key];
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

    public function fromArray($values) {
        foreach($values as $key => $value) {
            $this->set($key, $value);
        }
    }

}