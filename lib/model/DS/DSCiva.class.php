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

    public function addAppellation($appellation_hash) {   
      $produit_hash = preg_replace('/^\/recolte/','declaration',$appellation_hash);
      $appellationNode = $this->getOrAdd($produit_hash);
      $config = $appellationNode->getConfig();
      $appellationNode->libelle = $config->getLibelle();
     
      return $appellationNode;
    }

    public function addDetailsFromDRProduit($dr_produit) {
        foreach ($dr_produit->getProduitsDetails() as $detail) {
            $this->addDetail($dr_produit->getHash(), $detail->lieu);
        }
    }
    
    public function addProduit($hash) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $produit = $this->getOrAdd($hash);
        $config = $produit->getConfig();
        $produit->libelle = $config->getLibelle();
        $produit->getCouleur()->libelle = $produit->getConfig()->getCouleur()->libelle;
        $produit->getLieu()->libelle = $produit->getConfig()->getLieu()->libelle;
        $produit->getMention()->libelle = $produit->getConfig()->getMention()->libelle;
        $produit->getAppellation()->libelle = $produit->getConfig()->getAppellation()->libelle;
        $produit->no_vtsgn = (int) $config->hasVtsgn();
        return $produit;
    }

    public function addDetail($hash, $lieudit = null) {
        
        return $this->addProduit($hash)->addDetail($lieudit);
    }

    protected function updateProduitsFromDR($dr) {     
        $produits = $dr->getProduitsWithVolume();
        $this->drm_origine = $dr->_id;     
        foreach ($produits as $produit) {
            $this->addDetailsFromDRProduit($produit);              
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
        foreach ($appellation->getMentions() as $mention) {
            foreach ($mention->getLieux() as $lieu) {
                foreach ($lieu->getCouleurs() as $couleur) {
                    foreach ($couleur->getCepages() as $c) {
                            $cepage[$c->getHash()] = $c;
                    }

                }
            }
        }
        return $cepage;
    }
    
    public function getConfigAppellationsArray(){
        return array_keys($this->getConfigurationCampagne()->getArrayAppellations());
    }
    
    public function getAppellationsKeysArray(){
        return array_keys($this->getAppellationsArray());
    }
    
    public function getAppellationsArray() {
        $appellationsSorted = array();
        $appellations = $this->getAppellations()->toArray();        
        if(!count($appellations))
            throw new sfException(sprintf("La DS %s ne possède aucune appellation.",$this->_id));
        
        $config_appellations = $this->getConfigAppellationsArray();
        foreach ($config_appellations as $config_appellation_key) {            
            foreach ($appellations as $key => $appellation) {
                if(preg_match('/^appellation_/', $key) && ($key == $config_appellation_key)){
                    $appellationsSorted[preg_replace('/^appellation_/','', $key)] = $appellation;
                }
            }
        }
        
        return $appellationsSorted;
    }
    
    public function getAppellationsLieuKeysArray(){
        return array_keys($this->getAppellationsLieuArray());
    }
    
    public function getAppellationsLieuArray() {
        $appellationsSorted = array();
        $appellations = $this->getAppellations()->toArray();        
        if(!count($appellations))
            throw new sfException(sprintf("La DS %s ne possède aucune appellation.",$this->_id));
        
        $config_appellations = $this->getConfigAppellationsArray();
        foreach ($config_appellations as $config_appellation_key) {            
            foreach ($appellations as $key => $appellation) {
                if(preg_match('/^appellation_/', $key) && ($key == $config_appellation_key)){
                    
                    foreach ($appellation->getLieux() as $lieu_key => $lieu) {
                        $lieu_k = preg_replace('/^lieu/', '', $lieu_key);
                        $k = preg_replace('/^appellation_/', '', $key);
                        $k.= ($lieu_k!='')? '-'.$lieu_k : '';
                        $appellationsSorted[$k] = $lieu;
                    }
                }
            }
        }
        
        return $appellationsSorted;
    }


    public function getFirstAppellationLieu() {
        $appellations = $this->getAppellationsLieuKeysArray();
        if(!count($appellations))
            throw new sfException(sprintf("La DS %s ne possède aucune appellation.",$this->_id));
        return $appellations[0];
    }
    


    public function getNextAppellationLieu($appellation_lieu){
        $appellations = $this->getAppellationsLieuKeysArray();
        $next = false;           
        foreach ($appellations as $app) {
            if($app == $appellation_lieu){
                $next = true;
                continue;
            }
            if($next) return $app;
        }
        return null;
        
    }
    
     public function getPreviousAppellationLieu($appellation){
        $appellations = $this->getAppellationsLieuKeysArray();
        while($previous = array_pop($appellations)) {
                if($previous == $appellation){
                    return array_pop($appellations);
                }                
            }
        return null;
        
    }
    
    public function getAppellationLieuKey($appellation) {
        $appellations = $this->getAppellationsLieuKeysArray();
        foreach ($appellations as $app) {
            if(preg_replace('/-[A-Za-z0-9]*$/', '', $app) == $appellation){
                return $app;
            }           
        }
        return null;
    }

    public function hasManyLieux($appellation_key) {
        return (bool) (count($this->getLieuxFromAppellation($appellation_key)) > 1);
    }

    public function getLieuxFromAppellation($appellation_key) {
        $appellations = $this->getAppellations();
        foreach ($appellations as $key => $appelation_obj) {
            if(preg_replace('/^appellation_/', '', $key) == $appellation_key){
                return $appelation_obj->getLieux();
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
    
    public function getTotalAOC() {
        $total = 0;
        foreach ($this->getAppellations() as $hash => $appellation) {
            if(!preg_match('/^appellation_VINTABLE/', $hash))
                    $total += ($appellation->getTotalStock())? $appellation->getTotalStock() : 0;
        }
        return $total;
    }
    
    public function getTotalVinSansIg() {
        foreach ($this->getAppellations() as $hash => $appellation) {
            if(preg_match('/^appellation_VINTABLE/', $hash))
                    return ($appellation->getTotalStock())? $appellation->getTotalStock() : 0;
        }
        return 0;
    }
    
    
    public function isDsPrincipale() {
        return $this->getLieuStockage() == '001';
    }


    public function updateAutre($rebeches = 0,$dplc = 0,$lies = 0,$mouts = 0){
        if($this->isDsPrincipale()){
            $this->rebeches = $rebeches;
            $this->dplc = $dplc;
            $this->lies = $lies;
            $this->mouts = $mouts;
        }
    }
    
    public function getUsagesIndustriels() {
        return $this->lies + $this->dplc;
    }
    
}

