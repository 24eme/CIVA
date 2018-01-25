<?php
class VracBouteillesProduitsView extends acCouchdbView
{

	const KEY_NUMERO_ARCHIVE = 0;
	
	const VALUE_NUMERO_ARCHIVE = 0;
	const VALUE_NUMERO_ORDRE = 1;
	const VALUE_MILLESIME = 2;
	const VALUE_CODE_APPELLATION = 3;
	const VALUE_CEPAGE = 4;
	const VALUE_DENOMINATION = 5;
	const VALUE_NUM_AGREMENT = 6;
	const VALUE_CENTILISATION = 7;
	const VALUE_NB_BOUTEILLE = 8;
	const VALUE_VOLUME_ENLEVE = 9;
	const VALUE_PRIX_UNITAIRE = 10;
	const VALUE_VTSGN = 11;
	const VALUE_DENOMINATION = 12;

    public static function getInstance()
    {
        return acCouchdbManager::getView('VRAC', 'bouteilles_produits', 'Vrac');
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
