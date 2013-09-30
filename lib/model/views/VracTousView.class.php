<?php
class VracTousView extends acCouchdbView
{

    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'tous', 'Vrac');
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
}  