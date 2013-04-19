<?php

class ExportClient extends acCouchdbClient {
    
    public static function getInstance() {

        return acCouchdbManager::getClient('Export');
    }

    public function findAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->executeView("EXPORT", "tous", $hydrate);
    }

}
