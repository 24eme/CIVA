<?php

class RecoltantClient extends acCouchdbClient {

    public static function getInstance() {
    
        return acCouchdbManager::getClient('Recoltant'); 
    }
    
    /**
     *
     * @param string $cvi
     * @param type $hydrate
     * @return Recoltant 
     */
    public function retrieveByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('REC-'.$cvi, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return acCouchdbDocumentCollection 
     */
    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('REC-0000000000')->endkey('REC-99999999999')->execute($hydrate);
    }
}
