<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DSInterloire
 * @author mathurin
 */
class DSInterloire extends DS {
    
   
    public function getLastDocument() {
        return $this->getLastDRM();
    }
    
    private function getLastDRM() {
        
        return DRMClient::getInstance()->findLastByIdentifiantAndCampagne($this->identifiant, $this->campagne);
    }
    
    public function updateProduits() {
	if ($this->getEtablissement()->isViticulteur()) {
	  $drm = $this->getLastDRM();
	  if ($drm) {
	    return $this->updateProduitsFromDRM($drm); 
	  }
	}
	if ($this->getEtablissement()->isNegociant()) {
	  return $this->updateProduitsFromVracs(); 
	}
        $ds = $this->getLastDS();
        if ($ds) {
           return $this->updateProduitsFromDS($ds); 
        }	
    }
    
    public function addProduit($hash) {
        $config = ConfigurationClient::getCurrent()->get($hash);
        $produit = $this->declarations->add($config->getHashForKey());
        $produit->produit_hash = $config->getHash();
        $produit->updateProduit();

        return $produit;
    }
    
  protected function updateProduitsFromDRM($drm) {
        $produits = $drm->getProduits();
	    $this->drm_origine = $drm->_id;
        foreach ($produits as $produit) {
            $produitDs = $this->addProduit($produit->getHash());
            $produitDs->stock_initial = $produit->total;
        }
    }

    protected function updateProduitsFromVracs() {
      $hproduits = VracSoussigneIdentifiantView::getInstance()->getProduitHashesFromCampagneAndAcheteur($this->campagne, $this->getEtablissement());
      $hproduits = array_merge($hproduits, VracSoussigneIdentifiantView::getInstance()->getProduitHashesFromCampagneAndAcheteur(ConfigurationClient::getInstance()->getPreviousCampagne($this->campagne), $this->getEtablissement()));
      foreach ($hproduits as $produit) {
	$produitDs = $this->addProduit($produit);
      }
    }
    
    public function getCoordonnees() {
        return $this->getCoordonneesIL();
    }

    protected function getCoordonneesIL() {
        $configs = sfConfig::get('app_facture_emetteur');
        if (!array_key_exists($this->declarant->region, $configs))
            throw new sfException(sprintf('Config %s not found in app.yml', $this->declarant->region));
        return $configs[$this->declarant->region];
    }
    
    public function getEtablissement() {
        return EtablissementClient::getInstance()->find($this->identifiant);
    }


    public function getConfig() {
        return ConfigurationClient::getCurrent()->get($this->produit_hash);
    }

}
