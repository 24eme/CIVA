<?php

class CourtierClient extends acCouchdbClient {
    public static function getInstance()
    {
      return acCouchdbManager::getClient("Courtier");
    }  
	/**
     *
     * @param string $siren
     * @param type $hydrate
     * @return MetteurEnMarche 
     */
    public function retrieveBySiren($siren, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('COURT-'.$siren, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return acCouchdbDocumentCollection 
     */
    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('COURT-000000000')->endkey('COURT-999999999')->execute($hydrate);
    }
}
