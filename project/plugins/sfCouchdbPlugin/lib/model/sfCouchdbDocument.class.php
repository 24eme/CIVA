<?php

abstract class sfCouchdbDocument extends sfCouchdbJson {

    protected $_is_new = true;
    protected $_loaded_data = null;

    public function loadFromCouchdb(stdClass $data) {
        if (!is_null($this->_loaded_data)) {
            throw new sfCouchdbException("data already load");
        }
        if (isset($data->_attachments)) {
	       unset($data->_attachments);
        }

        $this->_loaded_data = serialize($data);
        $this->load($data);
    }

    public function __toString() {
        return $this->get('_id') . '/' . $this->get('_rev');
    }

    public function __construct() {
        parent::__construct(sfCouchdbManager::getDefinitionByHash($this->getDocumentDefinitionModel(), '/'), $this, "");
        $this->type = $this->getDefinition()->getModel();

        if (!$this->type) {
            throw new sfCouchdbException('Model should include Type field in the document root');
        }
    }

    public function isNew() {
        if (!$this->hasField('_rev'))
            return true;
        return is_null($this->get('_rev'));
    }

    public function save() {
        $this->definitionValidation();
        if ($this->isModified()) {
            $ret = sfCouchdbManager::getClient()->saveDocument($this);
            $this->_rev = $ret->rev;
            $this->_loaded_data = serialize($this->getData());
            return $ret;
        }
        return false;
    }

    public function getData() {
        $data = parent::getData();
        if ($this->isNew()) {
            unset($data->_rev);
        }
        return $data;
    }

    public function getDocumentDefinitionModel() {
        throw new sfCouchdbException('Definition model not implemented');
    }

    public function delete() {
        return sfCouchdbManager::getClient()->deleteDocument($this);
    }

    public function storeAttachment($file, $content_type = 'application/octet-stream', $filename = null) { 
      return sfCouchdbManager::getClient()->storeAttachment($this, $file, $content_type, $filename);
   }

    public function getAttachmentUri($filename) {
      return sfCouchdbManager::getClient()->dsn().sfCouchdbManager::getClient()->getAttachmentUri($this, $filename);
    }

    public function update($params = array()) {
        return parent::update($params);
    }

    public function isModified() {
        if ($this->isNew()) {
            return true;
        }

        $data_loaded = $this->sortStdClass(unserialize($this->_loaded_data));
        $data_final = $this->sortStdClass($this->getData());
        return (serialize($data_loaded) !== serialize($data_final));
    }

    private function sortStdClass($data) {
        $data = json_decode(json_encode($data), true);
        $this->deep_ksort($data);
        $data = json_decode(json_encode($data));
        return $data;
    }

    private function deep_ksort(&$arr) {
        ksort($arr, SORT_STRING);
        foreach ($arr as &$a) { 
            if (is_array($a) && !empty($a)) { 
                $this->deep_ksort($a); 
            } 
        } 
    } 

    public function __clone() {
        $this->_rev = null;
        $this->_id = null;
    }

}
