<?php

class MetteurEnMarcheClient extends acCouchdbClient {
    
    /**
     *
     * @param string $cvi
     * @param type $hydrate
     * @return MetteurEnMarche 
     */
    public function retrieveByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('MET-'.$cvi, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return acCouchdbDocumentCollection 
     */
    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('MET-0000000000')->endkey('MET-99999999999')->execute($hydrate);
    }
}
