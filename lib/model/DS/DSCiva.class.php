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
        $this->set('_id', DSClient::getInstance()->buildId($this->identifiant, $this->periode,'001'));
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

    public function addAppellation($appellation_hash, $config = null) {   
      $produit_hash = preg_replace('/^\/recolte/','declaration',$appellation_hash);
      $appellationNode = $this->getOrAdd($produit_hash);
     
      return $appellationNode;
    }
    
    public function addProduit($produit, $config = null) {   
      $produit_hash = preg_replace('/^\/recolte/','declaration',$produit->getHash());
      $detail = $this->getOrAdd($produit_hash)->addProduit($produit);     
      return $detail;
    }

    protected function updateProduitsFromDR($dr) {     
        $produits = $dr->getProduitsWithVolume();
        $this->drm_origine = $dr->_id;     
        foreach ($produits as $produitCepage) {
             $config = ConfigurationClient::getConfiguration()->get($produitCepage->getHash());
             $this->addProduit($produitCepage,$config);              
       }
    }
        
    
    public function getLieuStockage() {
        $matches = array();
        preg_match('/^DS-([0-9]{10})-([0-9]{6})-([0-9]{3})$/', $this->_id,$matches);
        return $matches[3];
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
        
    public function getProduits() {
        return $this->declaration->getProduitsDetails();
    }
    
    public function getProduitsByAppellation($appellation) {
        $cepage = array();
        $appellation = $this->declaration->getAppellation($appellation);
        $is_lieudit = preg_match('/_LIEUDIT$/',$appellation->getHash());
        foreach ($appellation->getMentions() as $mention) {
            foreach ($mention->getLieux() as $lieu) {
                foreach ($lieu->getCouleurs() as $couleur) {
                    foreach ($couleur->getCepages() as $c) {
                        if(!$is_lieudit)
                            $cepage[$c->getHash()] = $c;
                        else
                        {
                            $cepage_lieux = $c->getProduitsByLieuDits();
                            foreach($cepage_lieux as $lieu => $detail) { 
                                $lieu_key = KeyInflector::slugify($lieu);
                                $cepage[$c->getHash().'_'.$lieu_key] = $c;
                            }
                        }
                    }

                }
            }
        }
        return $cepage;
    }
    
    public function getFirstAppellation() {
        $appellations = $this->getAppellations()->toArray();
        if(!count($appellations))
            throw new sfException(sprintf("La DS %s ne possède aucune appellation.",$this->_id));
        foreach ($appellations as $key => $appellation) {
            if(preg_match('/^appellation_/', $key))
            return preg_replace ('/^appellation_/', '', $key);
        }
        return null;
    }
    
    public function getNextAppellation($appellation){
        $appellations = $this->getAppellations()->toArray();
        $next = false;
        foreach ($appellations as $key => $app) {
            if(preg_match('/^appellation_/', $key)){
                if(($app_key = preg_replace ('/^appellation_/', '', $key)) == $appellation){
                    $next = true;
                    continue;
                }
                if($next) return $app_key;
                
            }
        }
        return null;
        
    }
    
    public function getAppellations() {
        return $this->declaration->getAppellations();
    }
    
    public function getConfigurationCampagne() {
        return acCouchdbManager::getClient('Configuration')->retrieveConfiguration(substr($this->campagne,0,4));
    }
    
    public function getCepage($hash) {
        return $this->get($hash);
    }
    
}

