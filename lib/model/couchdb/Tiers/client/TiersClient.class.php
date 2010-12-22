<?php

class TiersClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('TIERS-'.$cvi, $hydrate);
    }
    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-0000000000')->endkey('TIERS-9999999999')->execute($hydrate);
    }

    public function getAllCivaba($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-C0000000000')->endkey('TIERS-C9999999999')->execute($hydrate);
    }
}
