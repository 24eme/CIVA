<?php
class VracProduitsView extends acCouchdbView
{

	const KEY_NUMERO_ARCHIVE = 0;
	
	const VALUE_NUMERO_ARCHIVE = 0;
	const VALUE_CODE_CEPAGE = 1;
	const VALUE_CEPAGE = 2;
	const VALUE_CODE_APPELLATION = 3;
	const VALUE_NUMERO_ORDRE = 4;
	const VALUE_VOLUME_PROPOSE = 5;
	const VALUE_VOLUME_ENLEVE = 6;
	const VALUE_PRIX_UNITAIRE = 7;
	const VALUE_DEGRE = 8;
	const VALUE_TOP_MERCURIALE = 9;
	const VALUE_MILLESIME = 10;
	const VALUE_VTSGN = 11;
	const VALUE_DATE_CIRCULATION = 12;
	const VALUE_DENOMINATION = 13;

    public static function getInstance()
    {
        return acCouchdbManager::getView('VRAC', 'produits', 'Vrac');
    }

    public function findBy($numeroArchive)
    {
		$startkey = array($numeroArchive);
		$endkey = array($numeroArchive, array());
        return $this->client->startkey($startkey)
                            ->endkey($endkey)
                            ->getView($this->design, $this->view)->rows;
    }

    public function findForDb2Export($numeroArchive)
    {
    	$result = array();
    	$items = $this->findBy($numeroArchive);
    	foreach ($items as $item) {
    		$result['P'.$item->value[self::VALUE_NUMERO_ORDRE]] = $item;
    	}
    	ksort($result);
    	return $result;
    }
}
