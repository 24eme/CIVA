<?php

class AcheteurClient extends sfCouchdbClient {
    public function getAll() {
        $docs = new sfCouchdbDocumentCollection($this->startkey('ACHAT-0000000000')->endkey('ACHAT-9999999999')->getAllDocs());
        return $docs;
    }
}
