<?php

class _CompteClient extends acCouchdbClient {
    
    /**
     *
     * @param string $login
     * @param integer $hydrate
     * @return Compte 
     */
    public function retrieveByLogin($login, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::find('COMPTE-'.$login, $hydrate);
    }
    
    /**
     *
     * @param integer $hydrate
     * @return acCouchdbDocumentCollection 
     */
    public function getAll($hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('COMPTE-')->endkey('COMPTE-C999999999')->execute($hydrate);
    }
}
