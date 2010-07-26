<?php
class sfCouchdbJsonArrayIterator extends ArrayIterator {
    private $_json;

    public function __construct(sfCouchdbJson $json)
    {
        $this->_json = $json;
        parent::__construct($json->getFields());
    }

    public function current() {
        return $this->_json->get($this->key());
    }

    public function offsetGet($index) {
        return $this->_json->offsetGet($index);
    }

    public function offsetSet($index, $newval) {
        return $this->_json->offsetSet($index, $newval);
    }

    public function  offsetExists($index) {
        return $this->_json->offsetExists($index);
    }

    public function offsetUnset($index) {
        return $this->_json->offsetUnset($index);
    }
}
?>