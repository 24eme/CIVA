<?php
class VracMailingView extends acCouchdbView
{
	const VALUE_DATE_SAISIE = 0;
	const VALUE_STATUT = 1;
	const VALUE_DOCUMENT_ID = 2;
	
    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'mailing', 'Vrac');
    }

    public function findBy($valide, $emailValide = null, $emailRelance = null) {    
		$params = array($valide);
		if ($emailValide !== null) {
			$params[] = $emailValide;
			if ($emailRelance !== null) {
				$params[] = $emailRelance;
			}
		}
		$startkey = $params;
		$params[] = array();
		$endkey = $params;
        return $this->client->startkey($startkey)
                            ->endkey($endkey)
                            ->getView($this->design, $this->view)->rows;
    }
    
    public function getContratsForEmailValide()
    {
    	$result = array();
    	$items = $this->findBy(1, 0);
    	foreach ($items as $item) {
    		$result[] = VracClient::getInstance()->find($item->value[self::VALUE_DOCUMENT_ID]);
    	}
    	return $result;
    }
    
    public function getContratsForEmailRelance($delai)
    {
    	$items = $this->findBy(1, 1, 0);
    }
    
    public function getContratsExpires($delai)
    {
    	$result = array();
    	$items = $this->findBy(0);
    	foreach ($items as $item) {
    		if ($item->value[self::VALUE_STATUT] == Vrac::STATUT_VALIDE_PARTIELLEMENT) {
    			if (strtotime(date('Y-m-d')) > strtotime(date("Y-m-d", strtotime($item->value[self::VALUE_DATE_SAISIE])) . " +".$delai." day")) {
    				$result[] = VracClient::getInstance()->find($item->value[self::VALUE_DOCUMENT_ID]);
    			}
    		}
    	}
    	return $result;
    }
}  