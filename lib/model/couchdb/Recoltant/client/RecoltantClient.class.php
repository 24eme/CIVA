<?php

class RecoltantClient extends sfCouchdbClient {
    
    /**
     *
     * @param string $cvi
     * @param type $hydrate
     * @return Recoltant 
     */
    public function retrieveByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('REC-'.$cvi, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return sfCouchdbDocumentCollection 
     */
    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('REC-0000000000')->endkey('REC-99999999999')->execute($hydrate);
    }
}
