<?php

class sfCouchdbJson implements IteratorAggregate, ArrayAccess, Countable {

    private $_fields = null;
    private $_is_array = false;
    protected $_definition_hash = null;
    protected $_definition_model = null;

    private $_couchdb_document = null;
    private $_object_hash = null;

    public function __construct($definition_model = null, $definition_hash = null, $couchdoc = null, $my_hash = null) {
        $this->_fields = array();
        $this->_is_array = false;
        if (is_null($definition_model) || is_null($definition_hash)) {
            $this->setupDefinition();
        } else {
            $this->_definition_model = $definition_model;
            $this->_definition_hash = $definition_hash;
        }
	if ($couchdoc) {
	  $this->_couchdb_document = $couchdoc;
	}
	if ($my_hash) {
	  $this->_object_hash = $my_hash;
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
            $ret = $this->addNumeric($item);
        } else {
            $ret = $this->addNormal($key, $item);
        }

	return $ret;
    }

    private function addNormal($key = null, $item = null) {
        if ($this->hasField($key)) {
            return $this->get($key);
        }

        $field = $this->getDefinition()->getJsonField($key, $item, false, $this->_couchdb_document, $this->_object_hash.'/'.$key);
        $this->_fields[$field->getKey()] = $field;
        return $field->getValue();
    }

    private function addNumeric($item = null) {
      $field = $this->getDefinition()->getJsonField(null, $item, true, $this->_couchdb_document, $this->_object_hash.'/'.count($this->_fields));
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

    private function hasFieldNormal($key) {
        return isset($this->_fields[sfInflector::underscore(sfInflector::camelize($key))]);
    }

    private function hasFieldNumeric($key) {
        return isset($this->_fields[$key]);
    }

    public function getField($key) {
        if ($this->_is_array) {
            return $this->getFieldNumeric($key);
        } else {
            return $this->getFieldNormal($key);
        }
    }

    public function getFields() {
        return $this->_fields;
    }

    private function getFieldNormal($key) {
         if ($this->hasField($key)) {
            return $this->_fields[sfInflector::underscore(sfInflector::camelize($key))];
        } else {
            throw new sfCouchdbException(sprintf('field inexistant : %s', $key));
        }
    }

    private function getFieldNumeric($key) {
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
            if (!is_array($value)) {
                $this->set($key, $value);
            }
        }
    }

    public function getSimpleFields() {
        $simple_fields = array();
        foreach($this->_fields as $key => $field) {
            if (!($field instanceof sfCouchdbJsonFieldArrayCollection || $field instanceof sfCouchdbJsonFieldCollection)) {
                $simple_fields[$key] = $field;
            }
        }
        return $simple_fields;
    }

    public function toArray($deep = false) {
        $array_fields = array();
        $simple_fields = $this->getSimpleFields();
        foreach($simple_fields as $key => $field) {
            $array_fields[$key] = $field->getValue();
        }

        return $array_fields;
    }

    public function setCouchdbDocumentAndHash($document, $hash, $internal = 0) {
      if ($this->_couchdb_document)
	return;
      $this->_couchdb_document = $document;
      $this->_object_hash = $hash;
      foreach($this->_fields as $key => $item) {
	$value = $item->getValue();
	if ($value instanceOf sfCouchdbJson) {
	  $value->setCouchdbDocumentAndHash($document, $hash.'/'.$key, 1);
	}
      }
      /*
      if (!$internal) {
	$this->update();
      }
      */
    }

    public function getCouchdbDocument() {
      if (!$this->_couchdb_document) {
	throw new sfCouchdbException('document not yet associated');
      }
      return $this->_couchdb_document;
    }

    public function setHash($hash) {
      $this->_object_hash = $hash;
    }

    public function getHash() {
      if (!$this->_object_hash) {
	throw new sfCouchdbException('document not yet associated');
      }
      return $this->_object_hash;
    }


    protected function update() {
      foreach ($this->_fields as $key => $item) {
	$value = $item->getValue();
	if ($value instanceOf sfCouchdbJson) {
	  $value->update();
	}
      }
    }

    public function getIterator() {
        return new sfCouchdbJsonArrayIterator($this);
    }

    public function offsetGet($index) {
        return $this->get($index);
    }

    public function offsetSet($index, $newval) {
        return $this->set($index, $newval);
    }

    public function offsetExists($index) {
        return $this->hasField($index);
    }

    public function offsetUnset($offset) {
      return $this->remove($offset);
    }

    public function count() {
        return count($this->_fields);
    }
}
