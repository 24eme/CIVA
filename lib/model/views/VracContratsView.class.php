<?php
class VracContratsView extends acCouchdbView
{

	const KEY_STATUT = 0;
	const KEY_DATE = 1;
	
	const VALUE_NUMERO_ARCHIVE = 0;
	const VALUE_TYPE_CONTRAT = 1;
	const VALUE_MERCURIALES = 2;
	const VALUE_MONTANT_COTISATION = 3;
	const VALUE_MONTANT_COTISATION_PAYE = 4;
	const VALUE_MODE_DE_PAIEMENT = 5;
	const VALUE_CVI_ACHETEUR = 6;
	const VALUE_TYPE_ACHETEUR = 7;
	const VALUE_TCA = 8;
	const VALUE_CVI_VENDEUR = 9;
	const VALUE_TYPE_VENDEUR = 10;
	const VALUE_NUMERO_CONTRAT = 11;
	const VALUE_DAA = 12;
	const VALUE_DATE_ARRIVEE = 13;
	const VALUE_DATE_TRAITEMENT = 14;
	const VALUE_DATE_SAISIE = 15;
	const VALUE_DATE_CIRCULATION = 16;
	const VALUE_IDENTIFIANT_COURTIER = 17;
	const VALUE_RECCOD = 18;
	const VALUE_TOTAL_VOLUME_PROPOSE = 19;
	const VALUE_TOTAL_VOLUME_ENLEVE = 20;
	const VALUE_QUANTITE_TRANSFEREE = 21;
	const VALUE_TOP_SUPPRESSION = 22;
	const VALUE_TOP_INSTANCE = 23;
	const VALUE_NOMBRE_CONTRATS = 24;
	const VALUE_HEURE_TRAITEMENT = 25;
	const VALUE_UTILISATEUR = 26;
	const VALUE_DATE_MODIF = 27;
	const VALUE_CREATION = 28;
	
    public static function getInstance() 
    {
        return acCouchdbManager::getView('VRAC', 'contrats', 'Vrac');
    }
    
    public function findAll()
    {
    	return $this->client->getView($this->design, $this->view)->rows;
    }

    public function findBy($statut, array $date) 
    {    
		$startkey = array($statut, $date[0]);
		$endkey = array($statut, $date[1], array());
        return $this->client->startkey($startkey)
                            ->endkey($endkey)
                            ->getView($this->design, $this->view)->rows;
    }
    
    public function findForDb2Export(array $date, $type = "C") 
    {
    	$contrats = array();
    	$statuts = ($type == "C")? array(Vrac::STATUT_VALIDE, Vrac::STATUT_ENLEVEMENT, Vrac::STATUT_CLOTURE) : array(Vrac::STATUT_ENLEVEMENT, Vrac::STATUT_CLOTURE);
		foreach ($statuts as $statut) {
			$contrats = array_merge($this->findBy($statut, $date), $contrats);
		}
		return $contrats;
    }
}  