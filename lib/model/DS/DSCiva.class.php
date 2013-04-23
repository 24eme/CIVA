<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DSCiva
 * @author mathurin
 */
class DSCiva extends DS {

    public function constructId() {
        if ($this->statut == null) {
            $this->statut = DSClient::STATUT_A_SAISIR;
        }
        $this->set('_id', DSClient::getInstance()->buildId($this->identifiant, $this->periode));
    }
    
    public function getLastDocument() {
        return $this->getLastDR();
    }

    private function getLastDR() {
        return DRClient::getInstance()->retrieveByCampagneAndCvi($this->identifiant, substr($this->campagne,0,4));
    }

    public function updateProduits() {
        $dr = $this->getLastDR();
        if ($dr) {
            return $this->updateProduitsFromDR($dr);
        }
        $ds = $this->getLastDS();
        if ($ds) {
            return $this->updateProduitsFromDS($ds);
        }
    }

    public function addProduit($hash) {
        $config = ConfigurationClient::getConfiguration()->get($hash);
        $produit = $this->declarations->add($config->getHashForKey());
        $produit->produit_hash = $config->getHash();
        $produit->updateProduit();

        return $produit;
    }

    protected function updateProduitsFromDR($dr) {
        $produits = $dr->getProduitsDetails();
        $this->drm_origine = $dr->_id;
        foreach ($produits as $produit) {
            $produitDs = $this->addProduit($produit->getHash());
            //$produitDs->stock_initial = $produit->total;
        }
    }
        

    public function getCoordonnees() {
        return $this->getCoordonneesCiva();
    }

    protected function getCoordonneesCiva() {
        $configs = sfConfig::get('app_facture_emetteur');
        if (!array_key_exists($this->declarant->region, $configs))
            throw new sfException(sprintf('Config %s not found in app.yml', $this->declarant->region));
        return $configs[$this->declarant->region];
    }

   public function getEtablissement() {
        return acCouchdbManager::getClient('Recoltant')->retrieveByCvi($this->identifiant);
    }

    public function getConfig() {
        return ConfigurationClient::getConfiguration()->get($this->produit_hash);
    }
    
}

