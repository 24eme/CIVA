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

    public function findBy($identifiant, $campagne = null, $statut = null, $type = null, $role = null)
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

    public function findSortedBy($identifiant, $campagne = null, $statut = null, $type = null, $role = null, $commercial = null) {
    	$items = $this->findBy($identifiant, $campagne, $statut, $type, $role);
    	$result = array();
    	foreach ($items as $item) {
            if($role && $item->value->role != $role) {
                continue;
            }
            if($commercial && $item->value->commercial != $commercial) {
                continue;
            }
    		$result[$item->id] = $item;
    	}
    	krsort($result);
    	return $result;
    }

    public function findSortedByDeclarants(array $tiers, $campagne = null, $statut = null, $type = null, $role = null, $commercial = null) {
        $result = array();
        foreach($tiers as $t) {
            foreach($this->findSortedBy($t->_id, $campagne, $statut, $type, $role, $commercial) as $key => $item) {
                if(isset($result[$key])) {
                    continue;
                }
                $result[$key] = $item;
            }
        }
        krsort($result);
        return $result;
    }
}
