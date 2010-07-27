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
    }
    public function retrieveDocById($id) {
      $data = $this->getDoc($id);
      $doc = new $data->type();
      $doc->load($data);
      return $doc;
    }
}
