<?php

class VracClient extends acCouchdbClient {
	
	const VRAC_PREFIXE_ID = 'VRAC-';
	const APP_CONFIGURATION = 'app_configuration_vrac';
	const APP_CONFIGURATION_PRODUITS = 'produits_statiques';
	const APP_CONFIGURATION_ETAPES = 'etapes';
	
    public static function getInstance()
    {
      return acCouchdbManager::getClient("Vrac");
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

    public function getAtDate($date, $hydrate = acCouchdbClient::HYDRATE_DOCUMENT) {
        return $this->startkey(self::VRAC_PREFIXE_ID.$date.'000')->endkey(self::VRAC_PREFIXE_ID.$date.'999')->execute($hydrate);
    } 
    
    public function createVrac($date = null) 
    {
    	$date = $this->getDate($date);
    	$config = self::getConfig();
        $campagne = ConfigurationClient::getInstance()->buildCampagne($date);
        $numeroContrat = $this->getNumeroContratSuivant($date);
        $vrac = new Vrac();
        $vrac->initVrac($config, $numeroContrat, $date, $campagne);
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
}
