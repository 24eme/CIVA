<?php

class sfCouchdbClient extends couchClient {

    const HYDRATE_ON_DEMAND = 1;
    const HYDRATE_ON_DEMAND_WITH_DATA = 2;
    const HYDRATE_JSON = 3;
    const HYDRATE_ARRAY = 4;
    const HYDRATE_DOCUMENT = 5;

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
    public function retrieveDocumentById($id, $hydrate = self::HYDRATE_DOCUMENT) {
        try {
             
             if ($hydrate == self::HYDRATE_DOCUMENT) {
                $data = $this->getDoc($id);
                return $this->createDocumentFromData($data);
             } elseif($hydrate == self::HYDRATE_JSON) {
                 return $this->getDoc($id);
             } elseif($hydrate == self::HYDRATE_ARRAY) {
                 return $this->asArray()->getDoc($id);
             } else {
                 throw new sfCouchdbException('This hyrdate method does not exist');
             }
        } catch (couchException $exc) {
             return null;
        }
        
    }
    public function createDocumentFromData($data) {
      if (!isset($data->type)) {
	throw new sfCouchdbException('data should have a type');
      }
      if (!class_exists($data->type)) {
	throw new sfCouchdbException('class '.$data->type.' not found');
      }
      $doc = new $data->type();
      $doc->load($data);
      return $doc;
    }

    public function execute($hydrate = self::HYDRATE_DOCUMENT) {
        if ($hydrate != self::HYDRATE_ON_DEMAND) {
            $this->include_docs(true);
        }
        if ($hydrate == self::HYDRATE_ARRAY) {
            $this->asArray();
        }
        return new sfCouchdbDocumentCollection($this->getAllDocs(), $hydrate);
    }
    
}
