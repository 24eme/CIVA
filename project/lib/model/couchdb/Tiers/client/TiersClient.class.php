<?php

class TiersClient extends acCouchdbClient {
    public function retrieveByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('TIERS-'.$cvi, $hydrate);
    }
    public function getAllCvi($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-0000000000')->endkey('TIERS-9999999999')->execute($hydrate);
    }

    public function getAllCivaba($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-C0000000000')->endkey('TIERS-C9999999999')->execute($hydrate);
    }

    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('TIERS-0')->endkey('TIERS-Z')->execute($hydrate);
    }

    public function getAllIds() {
        return $this->getAll(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
    }
}
