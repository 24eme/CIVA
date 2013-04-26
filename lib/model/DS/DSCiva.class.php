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

    public function addProduit($config, $produit = null) {   
        $key = $config->getHashForKey();
        $key .= ($produit->lieu!='')? '-'.KeyInflector::slugify($produit->lieu) : '';
        $produitNode = $this->declarations->add($key);
        $produitNode->produit_hash = $config->getHash();
        if($produit->lieu!=''){
            $produitNode->add('lieu', $produit->lieu);
        }
        
        $produitNode->add('vt', 0);
        $produitNode->add('sgn', 0);
        
        $vtsgn = '';
        if($produit->vtsgn){
            if($produit->vtsgn == 'VT') {
                $vtsgn = 'VT';
            }
            if($produit->vtsgn == 'SGN') {
                $vtsgn = 'SGN';
            }
        }
        $produitNode->stock_declare = 0;
        $produitNode->updateProduit($config,$vtsgn);
        return $produit;
    }

    protected function updateProduitsFromDR($dr) {     
        $produits = $dr->getProduitsDetails();
        $this->drm_origine = $dr->_id;
        foreach ($produits as $produit) {
            $config = ConfigurationClient::getConfiguration()->get($produit->getCepage()->getHash());
            if($produit){
                $this->addProduit($config, $produit);    
            }else
            $this->addProduit($config);            
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
    
    public function getLieuStockage(){
        return '01';
    }
    
}

