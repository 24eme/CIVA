<?php

class ExportClient extends sfCouchdbClient {
    
    public static function getInstance() {

        return sfCouchdbManager::getClient('Export');
    }

    public function findAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->executeView("EXPORT", "tous", $hydrate);
    }

}
