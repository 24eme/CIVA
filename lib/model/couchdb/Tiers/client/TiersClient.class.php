<?php

class TiersClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi) {
        return parent::retrieveDocumentById('TIERS-'.$cvi);
    }
    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-0000000000')->endkey('TIERS-9999999999')->execute($hydrate);
    }
}
