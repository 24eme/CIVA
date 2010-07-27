<?php

class sfCouchdbDocumentCollection implements IteratorAggregate, ArrayAccess, Countable {
    protected $_docs = array();
    public function  __construct($data = null) {
        $this->load($data);
    }
    private function load($data) {
        if (!is_null($data)) {
            try {
                foreach($data->rows as $item) {
                    $this->_docs[$item->id] = null;
                }
            } catch (Exception $exc) {
                throw new sfCouchdbException('Load error : data invalid');
            }
        }
    }

    public function getIds() {
        return array_keys($this->_docs);
    }

    public function getDocs() {
        return $this->_docs;
    }

    public function getIterator() {
        return new sfCouchdbDocumentCollectionArrayIterator($this);
    }

    public function get($id) {
        if($this->contains($id)) {
            if (is_null($this->_docs[$id])) {
                $this->_docs[$id] = sfCouchdbManager::getClient()->retrieveDocById($id);
            }
            return $this->_docs[$id];
        } else {
            print_r($this->_docs);
            throw new sfCouchdbException('This collection does not contains this id');
        }
    }

    public function contains($id) {
        return array_key_exists($id, $this->_docs);
    }

    public function remove($id) {
        if ($this->contains($id)) {
            unset($this->_docs[$id]);
            return true;
        } else {
            return false;
        }
    }

    public function offsetGet($index) {
        return $this->get($index);
    }

    public function offsetSet($index, $newval) {
        throw new sfCouchdbException('Do not set a document use add');
    }

    public function offsetExists($index) {
       return $this->contains($index);
    }

    public function offsetUnset($offset) {
        return $this->remove($offset);
    }

    public function count() {
        return count($this->_docs);
    }
}