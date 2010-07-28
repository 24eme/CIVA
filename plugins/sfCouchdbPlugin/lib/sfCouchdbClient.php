<?php

class sfCouchdbClient extends couchClient {
    public function saveDocument($document) {
        $method = 'POST';
	$url  = '/'.urlencode($this->dbname);
        if (!$document->isNew()) {
            $method = 'PUT';
            $url.='/'.urlencode($document->get('_id'));
	}
        return $this->_queryAndTest ($method, $url, array(200,201),array(),$document->getData());
    }

    public function deleteDocument($document) {
      return $this->deleteDoc($document->getData());

      $this->getList($id, $name, $view_name);
    }
    public function retrieveDocById($id) {
        try {
             $data = $this->getDoc($id);
             return $this->createDocumentFromData($data);
        } catch (Exception $exc) {
             return null;
        }
    }
    public function createDocumentFromData($data) {
      if (!isset($data->type)) {
	throw new sfCouchdbException('data should have a type');
      }
      $doc = new $data->type();
      $doc->load($data);
      return $doc;
    }
}
