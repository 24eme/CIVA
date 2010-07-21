<?php

class sfCouchdbDocument extends sfCouchdbJson {
    protected $_is_new = true;

    public function isNew() {
        return is_null($this->get('_rev'));
    }

    public function save() {
        return sfCouchdbManager::getClient()->saveDocument($this);
    }

    public function getData() {
        $data = parent::getData();
        if ($this->isNew()) {
            unset($data->_rev);
        }
        return $data;
    }
}