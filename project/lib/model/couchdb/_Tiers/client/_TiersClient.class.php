<?php

class _TiersClient extends acCouchdbClient {
    
    const STATUT_ACTIF = 'ACTIF';
    const STATUT_INACTIF = 'INACTIF';

    const QUALITE_RECOLTANT = 'Recoltant';
    const QUALITE_COOPERATIVE = 'Cooperative';
    const QUALITE_NEGOCIANT = 'Negociant';
    const QUALITE_COURTIER = 'Courtier';

    public static function getInstance() {
    
        return acCouchdbManager::getClient('_Tiers'); 
    }

    public function retrieveByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        
        $tiers = parent::find('REC-'.$cvi, $hydrate);

        if(!$tiers) {

            $tiers = parent::find('ACHAT-'.$cvi, $hydrate);
        }

        return $tiers;
    }

    public function findByCivaba($civaba, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return parent::find('MET-'.$civaba, $hydrate);
    }

    public function findBySiren($siren, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

         return parent::find('COURT-'.$siren, $hydrate);
    }

    public function findByCvi($cvi, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {

        return $this->retrieveByCvi($cvi);
    }

//    public function findByIdentifiant($identifiant) {
//
//        return $this->retrieveByCvi($identifiant);
//    }
    
    public function findByIdentifiant($identifiant) {
        $tiers = $this->retrieveByCvi($identifiant);        
        if(!$tiers){
            $tiers = $this->findByCivaba($identifiant); 
        }
        return $tiers;
    }
    
    public function findByIdentifiantNegoce($identifiant) {
        $tiers = $this->findByCivaba($identifiant); 
        if(!$tiers){
            $tiers = $this->retrieveByCvi($identifiant);        
        }
        return $tiers;
    }
}
