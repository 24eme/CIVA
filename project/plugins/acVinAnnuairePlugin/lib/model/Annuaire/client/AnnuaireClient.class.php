<?php

class AnnuaireClient extends acCouchdbClient
{
	const ANNUAIRE_PREFIXE_ID = 'ANNUAIRE-';
	const ANNUAIRE_RECOLTANTS_KEY = 'recoltants';
	const ANNUAIRE_NEGOCIANTS_KEY = 'negociants';
	const ANNUAIRE_CAVES_COOPERATIVES_KEY = 'caves_cooperatives';
 	static $annuaire_types = array(
 								self::ANNUAIRE_RECOLTANTS_KEY => 'Récoltant',
 								self::ANNUAIRE_NEGOCIANTS_KEY => 'Négociant',
 								self::ANNUAIRE_CAVES_COOPERATIVES_KEY => 'Cave coopérative'
 	);
 	static $tiers_qualites = array(
 								self::ANNUAIRE_RECOLTANTS_KEY => EtablissementFamilles::FAMILLE_PRODUCTEUR,
                                self::ANNUAIRE_NEGOCIANTS_KEY => EtablissementFamilles::FAMILLE_NEGOCIANT,
                                self::ANNUAIRE_CAVES_COOPERATIVES_KEY => EtablissementFamilles::FAMILLE_COOPERATIVE,
 	);

  	public static function getAnnuaireTypes()
  	{
  		return self::$annuaire_types;
  	}

  	public static function getTiersQualites()
  	{
  		return self::$tiers_qualites;
  	}

    public static function getInstance()
    {
      return acCouchdbManager::getClient("Annuaire");
    }

    public static function getTiersCorrespondanceType($tiersType)
    {
    	$types = self::getTiersCorrespondanceTypes();
    	return $types[$tiersType];
    }

    public function createAnnuaire($cvi)
    {
    	$annuaire = new Annuaire();
    	$annuaire->cvi = $cvi;
    	$annuaire->save();
    	return $annuaire;
    }

    public function findOrCreateAnnuaire($compte)
    {
        $cvi = $compte->login;

        if(preg_match("/^(C?[0-9]{10})[0-9]{2}$/", $cvi, $matches)) {
            $cvi = $matches[1];
        }

        $annuaire = $this->find(self::ANNUAIRE_PREFIXE_ID.$cvi);

        if ($annuaire) {
            return $annuaire;
        }

        if ($compte->_id != $compte->getMasterCompte()->_id) {
            return $this->findOrCreateAnnuaire($compte->getMasterCompte());
        }

    	return $this->createAnnuaire($cvi);
    }

    public function buildId($cvi)
    {
      return self::ANNUAIRE_PREFIXE_ID.$cvi;
    }

    public function findTiersByTypeAndIdentifiant($type, $identifiant, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT)
    {
        $tiers = EtablissementClient::getInstance()->find($identifiant);

		if(!$tiers) {
            $tiers = EtablissementClient::getInstance()->findByCvi($identifiant);
        }

        if(!$tiers) {
            $tiers = EtablissementClient::getInstance()->find("C".$identifiant);
        }

		if(!$tiers) {
			if($compte = CompteClient::getInstance()->find("COMPTE-".$identifiant)) {
				$tiers = $compte->getSociete()->getEtablissementPrincipal();
			}
		}

        if(!$tiers) {

            return null;
        }

        $tiersQualites = self::getTiersQualites();

        if(!preg_match("/".$tiersQualites[$type]."/", $tiers->getFamille())) {

            return null;
        }

        if(!$tiers->isActif()) {

            return null;
        }

        return $tiers;
    }
}
