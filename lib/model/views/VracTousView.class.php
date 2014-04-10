<?php
class VracTousView extends acCouchdbView
{

    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'tous', 'Vrac');
    }
    
    public function findAll()
    {
    	return $this->client->getView($this->design, $this->view)->rows;
    }

    public function findBy($identifiant, $campagne = null, $statut = null, $type = null) 
    { 
    	if ($type) {
    		$types = array($type);
    	} else {
    		$types = array_keys(VracClient::getContratTypes());
    	}
    	$result = array();
    	foreach ($types as $t) {
			$params = array($identifiant);
			$params[] = $t;
			if ($campagne) {
				$params[] = $campagne;
				if ($statut) {
					$params[] = $statut;
				}
			}
			$startkey = $params;
			$params[] = array();
			$endkey = $params;
	        $result = array_merge($result, $this->client->startkey($startkey)
	                            ->endkey($endkey)
	                            ->getView($this->design, $this->view)->rows);
    	}
    	return $result;
    }
    
    public function findSortedBy($identifiant, $campagne = null, $statut = null, $type = null) {
    	$items = $this->findBy($identifiant, $campagne, $statut, $type);
    	$result = array();
    	foreach ($items as $item) {
    		$result[$item->id] = $item;
    	}
    	krsort($result);
    	return $result;
    }

    public function findSortedByDeclarants(array $tiers, $campagne = null, $statut = null, $type = null) {
        $result = array();
        foreach($tiers as $t) {
            $result = array_merge($result, $this->findSortedBy($t->_id, $campagne, $statut, $type));
        }
        krsort($result);
        return $result;
    }
}  