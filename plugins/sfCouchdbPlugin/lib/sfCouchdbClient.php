<?php

class sfCouchdbClient extends couchClient {
    public function saveDocument($document) {
        $method = 'POST';
	$url  = '/'.urlencode('dr');
        if (!$document->isNew()) {
            $method = 'PUT';
            $url.='/'.urlencode($document->get('_id'));
	}
        return $this->_queryAndTest ($method, $url, array(200,201),array(),$document->getData());
    }
}
