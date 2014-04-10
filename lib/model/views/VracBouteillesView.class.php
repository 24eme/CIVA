<?php
class VracBouteillesView extends acCouchdbView
{

	const KEY_TYPE = 0;
	const KEY_STATUT = 1;
	const KEY_DATE = 2;
	
	const VALUE_NUMERO_ARCHIVE = 0;
	const VALUE_CVI_VENDEUR = 1;
	const VALUE_TYPE_ACHETEUR = 2;
	const VALUE_CVI_ACHETEUR = 3;
	const VALUE_TYPE_VENDEUR = 4;
	const VALUE_IDENTIFIANT_COURTIER = 5;
	const VALUE_DAA = 6;
	const VALUE_TOTAL_VOLUME_ENLEVE = 7;
	const VALUE_NUMERO_CONTRAT = 8;
	const VALUE_DATE_ARRIVEE = 9;
	const VALUE_DATE_CONTRAT = 10;
	const VALUE_DATE_TRAITEMENT = 11;
	const VALUE_DATE_MODIFICATION = 12;
	const VALUE_TOP_INSTANCE = 13;
	const VALUE_TOP_SUPPRESSION = 14;
	const VALUE_UTILISATEUR = 15;
	const VALUE_CREATION = 16;
	
    public static function getInstance() 
    {
        return acCouchdbManager::getView('VRAC', 'bouteilles', 'Vrac');
    }
    
    public function findAll()
    {
    	return $this->client->startkey(array(VracClient::TYPE_BOUTEILLE))->endkey(array(VracClient::TYPE_BOUTEILLE, array()))->getView($this->design, $this->view)->rows;
    }

    public function findBy($statut, array $date) 
    {    
		$startkey = array(VracClient::TYPE_BOUTEILLE, $statut, $date[0]);
		$endkey = array(VracClient::TYPE_BOUTEILLE, $statut, $date[1], array());
        return $this->client->startkey($startkey)
                            ->endkey($endkey)
                            ->getView($this->design, $this->view)->rows;
    }
    
    public function findForDb2Export(array $date) 
    {
		return $this->findBy(Vrac::STATUT_CLOTURE, $date);
    }
}  