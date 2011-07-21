<?php

class MetteurEnMarcheClient extends sfCouchdbClient {
    
    /**
     *
     * @param string $cvi
     * @param type $hydrate
     * @return MetteurEnMarche 
     */
    public function retrieveByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('MET-'.$cvi, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return sfCouchdbDocumentCollection 
     */
    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('MET-0000000000')->endkey('MET-99999999999')->execute($hydrate);
    }
}
