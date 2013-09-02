<?php
/**
 * Model for Vrac
 *
 */

class Vrac extends BaseVrac 
{
	
	const STATUT_CREE = 'CREE';
	const STATUT_VALIDE_PARTIELLEMENT = 'VALIDE_PARTIELLEMENT';
	const STATUT_VALIDE = 'VALIDE';
	const STATUT_ANNULE = 'ANNULE';
	const STATUT_ENLEVEMENT = 'ENLEVEMENT';
	const STATUT_CLOTURE = 'CLOTURE';
	
	protected $_config;
    
    public function init($config, $numeroContrat, $date, $campagne)
    {
    	
        $this->_config = $config;
        $this->campagne = $campagne;
        $this->numero_contrat = $numeroContrat;
        $this->valide->date_saisie = $date;
        $this->valide->statut = self::STATUT_CREE;
        $this->initProduits();
    }

    public function constructId() 
    {
        $this->set('_id', VracClient::getInstance()->buildId($this->numero_contrat));
    }
    
	public function getConfiguration() 
	{
        $campagne = substr($this->campagne,0,4);      
        $conf_2012 = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
        if($campagne > '2012'){
            return $conf_2012;
        }        
        $conf = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
        return $conf;
     }

    protected function initProduits() 
    {
    	if (isset($this->_config[VracClient::APP_CONFIGURATION_PRODUITS])) {
    		$produits = $this->_config[VracClient::APP_CONFIGURATION_PRODUITS];
    		foreach ($produits as $produit_hash => $produit_config) {
    			$this->addDetail($produit_hash, $produit_config);
    		}
    	}
    }
    
	public function addProduit($hash) 
	{
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash_config = preg_replace('/^declaration/','recolte', $hash);
        $produit = $this->getOrAdd($hash);
        $config = $produit->getConfig();
        $produit->libelle = $config->getLibelle();
        $produit->getCouleur()->libelle = $produit->getConfig()->getCouleur()->libelle;
        $produit->getLieu()->libelle = $produit->getConfig()->getLieu()->libelle;
        $produit->getMention()->libelle = $produit->getConfig()->getMention()->libelle;
        $produit->getAppellation()->libelle = $produit->getConfig()->getAppellation()->libelle;
        $produit->no_vtsgn = (int) !$config->hasVtsgn();
        return $produit;
    }

    public function addDetail($hash, $config = null) 
    {
        $produit = $this->addProduit($hash);
        if(!$produit) {
            return null;
        }
        return $produit->addDetail($config);
    }
}