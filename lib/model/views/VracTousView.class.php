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

    public function findBy($identifiant, $campagne = null, $statut = null) {    
		$params = array($identifiant);
		if ($campagne) {
			$params[] = $campagne;
			if ($statut) {
				$params[] = $statut;
			}
		}
		$startkey = $params;
		$params[] = array();
		$endkey = $params;
        return $this->client->startkey($startkey)
                            ->endkey($endkey)
                            ->getView($this->design, $this->view)->rows;
    }
    
    public function findSortedBy($identifiant, $campagne = null, $statut = null) {
    	$items = $this->findBy($identifiant, $campagne, $statut);
    	$result = array();
    	foreach ($items as $item) {
    		$result[$item->id] = $item;
    	}
    	krsort($result);
    	return $result;
    }

    public function findSortedByDeclarants(array $tiers, $campagne = null, $statut = null) {
        $result = array();
        foreach($tiers as $t) {
            $result = array_merge($result, $this->findSortedBy($t->_id, $campagne, $statut));
        }
        krsort($result);
        return $result;
    }
}  