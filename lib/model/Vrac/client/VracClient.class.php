<?php

class VracClient extends acCouchdbClient {
	
	const VRAC_PREFIXE_ID = 'VRAC-';
	const APP_CONFIGURATION = 'app_configuration_vrac';
	const APP_CONFIGURATION_PRODUITS = 'produits_statiques';
	const APP_CONFIGURATION_ETAPES = 'etapes';
	const NB_MAX_CONTRAT_DB2 = 99999;
	
    public static function getInstance()
    {
      return acCouchdbManager::getClient("Vrac");
    }

    public static function canBeHaveVrac($tiers)
    {
      if($tiers->type == 'MetteurEnMarche' && $tiers->hasAcheteur()) {

        return false;
      }

      return true;
    }
    
    public static function canBeCreate($tiers)
    {
    	if ($tiers->type == 'Acheteur' || $tiers->type == 'Courtier') {
    		return true;
    	}
    	if ($tiers->type == 'MetteurEnMarche' && !$tiers->hasAcheteur()) {
    		return true;
    	}
    	return false;
    }
    
    public static function getConfig()
    {
    	if ($config = sfConfig::get(self::APP_CONFIGURATION)) {
    		return $config;
    	}
    	throw new sfException('Aucune configuration vrac définie dans l\'app!');
    }
    
	public function buildId($numero_contrat) {
        return sprintf(self::VRAC_PREFIXE_ID.'%s', $numero_contrat);
    }
    
    protected function getDate($date = null)
    {
    	return ($date)? $date : date('Y-m-d');
    }
    
    protected function getCampagne($campagne = null)
    {
    	return ($campagne)? $campagne : ConfigurationClient::getInstance()->buildCampagne($this->getDate());
    }
    
    public function getNumeroContratSuivant($date = null)
    {
    	$date = $this->getDate($date);
        $date = date('Ymd', strtotime($date));
        $contrats = $this->getAtDate($date, acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        $id = sprintf('%s%03d', $date, 1);
        if (count($contrats) > 0) {
        	$suffixe = substr(max($contrats), -3) + 1;
            $id = sprintf('%s%03d', $date, $suffixe);
        }
        return $id;
    }
    
    public function getNumeroSuivant()
    {
        $contrats = VracTousView::getInstance()->findAll();
        $numeroSuivant = count($contrats) + 1;
        if ($numeroSuivant > self::NB_MAX_CONTRAT_DB2) {
        	throw new sfException('ATTENTION : Limite de contrat possible pour db2 atteinte (plus aucun contrat ne peut être créé).');
        }
        return sprintf('%05d', $numeroSuivant);
    }

    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(self::VRAC_PREFIXE_ID.$date.'000')->endkey(self::VRAC_PREFIXE_ID.$date.'999')->execute($hydrate);
    } 
    
    public function createVrac($createurIdentifiant, $date = null) 
    {
    	$date = $this->getDate($date);
    	$config = self::getConfig();
        $campagne = ConfigurationClient::getInstance()->buildCampagne($date);
        $numeroContrat = $this->getNumeroContratSuivant($date);
        $vrac = new Vrac();
        $vrac->initVrac($config, $createurIdentifiant, $numeroContrat, $date, $campagne);
        return $vrac;
    } 
    
    public function findByNumeroContrat($numeroContrat) 
    {
      return $this->find($this->getId($numeroContrat));
    }


    public function getId($numeroContrat)
    {
      return self::VRAC_PREFIXE_ID.$numeroContrat;
    }

  	
  	public function getStatutLibelle($statut)
  	{
  		$libelles = Vrac::getStatutsLibelles();
  		return $libelles[$statut];
  	}
  	
  	public function getStatutLibelleAction($statut, $proprietaire = false, $hasValidated = false)
  	{
  		$libelles = Vrac::getStatutsLibellesActions();
  		if ($proprietaire) {
  			$libelles = Vrac::getStatutsLibellesActionsProprietaire();
  		}
  		if ($statut == Vrac::STATUT_VALIDE_PARTIELLEMENT && $hasValidated) {
  			return $libelles[Vrac::STATUT_CLOTURE];
  		}
  		return ($statut)? $libelles[$statut] : $libelles[VRAC::STATUT_CREE];
  	}
}
