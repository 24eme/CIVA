<?php

class TiersClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi) {
        return parent::retrieveDocumentById('TIERS-'.$cvi);
    }
    public function getAll($with_doc = false, $couchdb_doc = false) {
        $query_data = $this->startkey('TIERS-0000000000')->endkey('TIERS-9999999999');
        if ($with_doc) {
            $query_data->include_docs(true);
        }
        $docs = new sfCouchdbDocumentCollection($query_data->getAllDocs(), $with_doc, $couchdb_doc);
        return $docs;
    }
}
