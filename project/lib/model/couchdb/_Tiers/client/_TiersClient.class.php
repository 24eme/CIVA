<?php

class _TiersClient extends acCouchdbClient {
    
    const STATUT_ACTIF = 'ACTIF';
    const STATUT_INACTIF = 'INACTIF';

    public function retrieveByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        
        $tiers = parent::find('REC-'.$cvi, $hydrate);

        if(!$tiers) {

            $tiers = parent::find('ACHAT-'.$cvi, $hydrate);
        }

        return $tiers;
    }

    public function findByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->retrieveByCvi($cvi);
    }

    public function findByIdentifiant($identifiant) {

        return $this->retrieveByCvi($identifiant);
    }
}
