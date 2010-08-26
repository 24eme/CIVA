<?php

class RecoltantClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi) {
        return parent::retrieveDocumentById('REC-'.$cvi);
    }
    public function getAll() {
        $docs = new sfCouchdbDocumentCollection($this->startkey('REC-0000000000')->endkey('REC-9999999999')->getAllDocs());
        return $docs;
    }
}
