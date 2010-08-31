<?php

class sfCouchdbJson implements IteratorAggregate, ArrayAccess, Countable {

    private $_fields = null;
    private $_is_array = false;
    protected $_definition_hash = null;
    protected $_definition_model = null;
    private $_couchdb_document = null;
    private $_object_hash = null;
    private $_filter = null;
    private $_filter_persisent = false;

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
        foreach ($this->getDefinition()->getRequiredFields() as $field_definition) {
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
                if (!$this->hasField($key)) {
                    $this->add($key);
                }
                $this->_set($key, $item);
            }
        }
    }

    public function __get($key) {
        return $this->get($key);
    }

    protected function _get($key) {
        return $this->getField($key)->getValue();
    }

    public function get($key_or_hash) {
        $obj_hash = new sfCouchdbHash($key_or_hash);
        if ($obj_hash->isAlone()) {
            if (!$this->isArray() && $this->hasAccessor($obj_hash->getFirst())) {
                $method = $this->getAccessor($obj_hash->getFirst());
                return $this->$method();
            }
            return $this->_get($obj_hash->getFirst());
        } else {
            return $this->get($obj_hash->getFirst())->get($obj_hash->getAllWithoutFirst());
        }
    }

    private function setFromDataOrObject($key, $data_or_object) {
        $field = $this->getField($key);
        if ($data_or_object instanceof sfCouchdbJson) {
            $field->getValue()->load($data_or_object->getData());
        } elseif ($data_or_object instanceof stdClass) {
            $field->getValue()->load($data_or_object);
        } elseif (is_array($data_or_object)) {
            $field->getValue()->load($data_or_object);
        } else {
            $field->setValue($data_or_object);
        }

        return $field;
    }

    public function _set($key, $value) {
        return $this->setFromDataOrObject($key, $value);
    }

    public function set($key_or_hash, $value) {
        $obj_hash = new sfCouchdbHash($key_or_hash);
        if ($obj_hash->isAlone()) {
            if (!$this->isArray() && $this->hasMutator($obj_hash->getFirst())) {
                $method = $this->getMutator($obj_hash->getFirst());
                return $this->$method($value);
            }
            return $this->_set($obj_hash->getFirst(), $value);
        } else {
            return $this->get($obj_hash->getFirst())->set($obj_hash->getAllWithoutFirst(), $value);
        }
    }

    public function remove($key_or_hash) {
        $obj_hash = new sfCouchdbHash($key_or_hash);
        if ($obj_hash->isAlone()) {
            if ($this->_is_array) {
                return $this->removeNumeric($key_or_hash);
            } else {
                return $this->removeNormal($key_or_hash);
            }
        }
        return $this->getField($obj_hash->getFirst())->getValue()->remove($obj_hash->getAllWithoutFirst());
    }

    private function removeNormal($key) {
        if ($this->hasField($key)) {
            unset($this->_fields[sfInflector::underscore(sfInflector::camelize($key))]);
            return true;
        }
        return false;
    }

    private function removeNumeric($key) {
        if ($this->hasField($key)) {
            unset($this->_fields[$key]);
            return true;
        }
        return false;
    }

    public function clear() {
        if ($this->_is_array) {
            foreach($this->_fields as $key => $field) {
                $this->remove($key);
            }
        }
    }

    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    public function add($key = null, $item = null) {
        if ($this->_is_array) {
            $ret = $this->addNumeric();
        } else {
            $ret = $this->addNormal($key);
        }
        if (!is_null($item)) {
            if ($this->_is_array) {
                $this->set(count($this->_fields) - 1, $item);
            } else {
                $this->set($key, $item);
            }
        }
        return $ret;
    }

    private function addNormal($key = null) {
        if ($this->hasField($key)) {
            return $this->get($key);
        }

        $field = $this->getDefinition()->getJsonField($key, false, $this->_couchdb_document, $this->_object_hash . '/' . $key);
        $this->_fields[$field->getKey()] = $field;
        return $field->getValue();
    }

    private function addNumeric() {
        $field = $this->getDefinition()->getJsonField(null, true, $this->_couchdb_document, $this->_object_hash . '/' . count($this->_fields));
        $this->_fields[] = $field;

        return $field->getValue();
    }

    public function exist($key) {
        return $this->hasField($key);
    }

    protected function hasField($key) {
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
	  throw new sfCouchdbException(sprintf('field inexistant : %s (%s)', $key, $this->getHash()));
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

    protected function getModelAccessor() {
        if (!isset(sfCouchdbManager::getInstance()->_custom_accessors[$this->_definition_model][$this->_definition_hash])) {
            sfCouchdbManager::getInstance()->_custom_accessors[$this->_definition_model][$this->_definition_hash] = array();
        }
        return sfCouchdbManager::getInstance()->_custom_accessors[$this->_definition_model][$this->_definition_hash];
    }

    protected function getModelMutator() {
        if (!isset(sfCouchdbManager::getInstance()->_custom_mutators[$this->_definition_model][$this->_definition_hash])) {
            sfCouchdbManager::getInstance()->_custom_mutators[$this->_definition_model][$this->_definition_hash] = array();
        }
        return sfCouchdbManager::getInstance()->_custom_mutators[$this->_definition_model][$this->_definition_hash];
    }

    protected function hasAccessor($key) {
        $fieldName = sfInflector::underscore(sfInflector::camelize($key));
        $model_accessor = $this->getModelAccessor();

        if (isset($model_accessor[$fieldName]) && is_null($model_accessor[$fieldName])) {
            return false;
        } elseif (isset($model_accessor[$fieldName]) && !is_null($model_accessor[$fieldName])) {
            return true;
        } else {
            $accessor = 'get' . sfInflector::camelize($fieldName);
            if ($accessor != 'get' && method_exists($this, $accessor)) {
                sfCouchdbManager::getInstance()->_custom_accessors[$this->_definition_model][$this->_definition_hash][$fieldName] = $accessor;
                return true;
            } else {
                sfCouchdbManager::getInstance()->_custom_accessors[$this->_definition_model][$this->_definition_hash][$fieldName] = null;
                return false;
            }
        }
    }

    public function getAccessor($key) {
        $fieldName = sfInflector::underscore(sfInflector::camelize($key));
        $model_accessor = $this->getModelAccessor();
        if ($this->hasAccessor($fieldName)) {
            return $model_accessor[$fieldName];
        }
    }

    protected function hasMutator($key) {
        $fieldName = sfInflector::underscore(sfInflector::camelize($key));
        $model_mutator = $this->getModelMutator();
        if (isset($model_mutator[$fieldName]) && is_null($model_mutator[$fieldName])) {
            return false;
        } elseif (isset($model_mutator[$fieldName]) && !is_null($model_mutator[$fieldName])) {
            return true;
        } else {
            $mutator = 'set' . sfInflector::camelize($fieldName);
            if ($mutator != 'set' && method_exists($this, $mutator)) {
                sfCouchdbManager::getInstance()->_custom_mutators[$this->_definition_model][$this->_definition_hash][$fieldName] = $mutator;
                return true;
            } else {
                sfCouchdbManager::getInstance()->_custom_mutators[$this->_definition_model][$this->_definition_hash][$fieldName] = null;
                return false;
            }
        }
    }

    protected function getMutator($key) {
        $fieldName = sfInflector::underscore(sfInflector::camelize($key));
        $model_mutator = $this->getModelMutator();
        if ($this->hasMutator($fieldName)) {
            return $model_mutator[$fieldName];
        }
    }

    public function getData() {

        $data = array();

        foreach ($this->_fields as $field) {
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
        foreach ($values as $key => $value) {
            if (!is_array($value)) {
                $this->set($key, $value);
            }
        }
    }

    public function getSimpleFields() {
        $simple_fields = array();
        foreach ($this->_fields as $key => $field) {
            if (!$field->isCollection()) {
                $simple_fields[$key] = $field;
            }
        }
        return $simple_fields;
    }

    public function toArray($deep = false) {
        $array_fields = array();
        if ($deep === false || $deep < 1) {
            $simple_fields_keys = array_keys($this->getSimpleFields());
            foreach ($simple_fields_keys as $key) {
                $v = $this->get($key);
                $array_fields[$key] = $v;
            }
        } elseif ($deep) {
            foreach ($this->_fields as $key => $field) {
                if (!$field->isCollection()) {
                    $array_fields[$key] = $this->get($key);
                } else {
                    $array_fields[$key] = $field->getValue()->toArray($deep - 1);
                }
            }
        }
        return $array_fields;
    }

    public function setCouchdbDocumentAndHash($document, $hash, $internal = 0) {
        if ($this->_couchdb_document)
            return;
        $this->_couchdb_document = $document;
        $this->_object_hash = $hash;
        foreach ($this->_fields as $key => $item) {
            $value = $item->getValue();
            if ($value instanceOf sfCouchdbJson) {
                $value->setCouchdbDocumentAndHash($document, $hash . '/' . $key, 1);
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

    public function getParentHash() {
        return preg_replace('/\/[^\/]+$/', '', $this->getHash());
    }

    public function getParent() {
      return $this->getCouchdbDocument()->get($this->getParentHash());
    }

    public function getKey() {
      return preg_replace('/^.*\//', '\1', $this->getHash());
    }

    public function getHash() {
        if (!$this->_object_hash) {
            throw new sfCouchdbException('document not yet associated');
        }
        return $this->_object_hash;
    }

    public function getFirst() {
        return $this->getIterator()->getFirst();
    }

    public function getFirstKey() {
        return $this->getIterator()->getFirstKey();
    }
    
    public function getLast() {
        return $this->getIterator()->getLast();
    }

    public function getLastKey() {
        return $this->getIterator()->getLastKey();
    }

    protected function update() {
        foreach ($this->_fields as $key => $item) {
            $value = $item->getValue();
            if ($value instanceOf sfCouchdbJson) {
                $value->update();
            }
        }
    }

    public function filter($expression, $persisent = false) {
        $this->_filter = $expression;
        $this->_filter_persisent = $persisent;
        return $this;
    }

    public function clearFilter() {
        $this->_filter = null;
        $this->_filter_persisent = false;
    }

    public function getIterator() {
        $iterator = new sfCouchdbJsonArrayIterator($this, $this->_filter);
        if (!$this->_filter_persisent) {
            $this->clearFilter();
        }
        return $iterator;
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
        return $this->getIterator()->count();
    }

}
