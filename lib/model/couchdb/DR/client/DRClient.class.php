<?php

class DRClient extends sfCouchdbClient {
    public function retrieveByCampagneAndCvi($cvi, $campagne, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return parent::retrieveDocumentById('DR-'.$cvi.'-'.$campagne, $hydrate);
    }

    public function getAllByCampagne($campagne, $hydrate = sfCouchdbClient::HYDRATE_ON_DEMAND) {
        $docs = $this->getAll($hydrate);
        $i = 0;
        $keys = array_keys($docs->getDocs());
        foreach($keys as $key) {
            //echo substr($key, strlen($key) - 4, 4);
            if (substr($key, strlen($key) - 4, 4) != $campagne) {
                unset($docs[$key]);
            }
        }
        return $docs;
    }

    public function getAllByCvi($cvi, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
       return $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-2020')->execute($hydrate);
    }

    /**
     *
     * @param string $cvi
     * @param string $campagne
     * @return array 
     */
    public function getArchivesSince($cvi, $campagne) {
        $docs = $this->startkey('DR-'.$cvi.'-0000')->endkey('DR-'.$cvi.'-'.$campagne)->execute(sfCouchdbClient::HYDRATE_ON_DEMAND);
        $campagnes = array();
        foreach($docs->getIds() as $doc_id) {
            preg_match('/DR-(?P<cvi>\d+)-(?P<campagne>\d+)/', $doc_id, $matches);
            $campagnes[$doc_id] = $matches['campagne'];
        }
        return $campagnes;
    }

    public function getAll($hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey('DR-0000000000-0000')->endkey('DR-9999999999-9999')->execute($hydrate);
    }
    
    public function findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(array($campagne, $cvi_acheteur))->endkey(array($campagne, (string)($cvi_acheteur + 1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }
    
    public function findAllByCampagneAcheteurs($campagne, $hydrate = sfCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(array((string)$campagne))->endkey(array((string)($campagne+1)))->executeView("DR", "campagne_acheteur", $hydrate);
    }
}
