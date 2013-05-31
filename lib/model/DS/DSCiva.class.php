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

    public function addNoeud($hash) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $noeud = $this->getOrAdd($hash);
        $config = $noeud->getConfig();
        $noeud->libelle = $config->getLibelle();

        if($noeud instanceof DSCepage && !$config->getParent()->hasManyNoeuds() && !$config->hasLieuEditable()) {
            $this->addDetail($hash);
        }

        if(!$config->hasManyNoeuds() && count($config->getChildrenNode()) > 0) {
            $this->addNoeud($config->getChildrenNode()->getFirst()->getHash());
        }

        return $noeud;
    }

    public function addAppellation($hash) {   

        return $this->addNoeud($hash);
    }

    public function addLieu($hash) {

        return $this->addNoeud($hash);
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
        $produit->no_vtsgn = (int) !$config->hasVtsgn();
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

    public function getFirstAppellation() {
        $appellations = $this->declaration->getAppellationsSorted();
        if(!count($appellations))
            throw new sfException(sprintf("La DS %s ne possède aucune appellation.",$this->_id));
        
        return current($appellations);
    }
    
    public function getNextLieu($lieu){
        $appellation = $lieu->getAppellation();
        $appellations = $appellation->getParent()->getAppellationsSorted();
        $lieux = $lieu->getParent()->getLieuxSorted();
        $next = false;
        foreach ($lieux as $hash => $l) {
            if($l->getHash() == $lieu->getHash()){
                $next = true;
                continue;
            }
            if($next) {
                return $l;
            }
        }
        $next = false;
        foreach($appellations as $hash => $a) {
            if($a->getHash() == $appellation->getHash()){
                $next = true;
                continue;
            }
            if($next) {
                return $a;
            }
        }
        return null;
    }
    
     public function getPreviousLieu($lieu){
        $appellation = $lieu->getAppellation();
        $appellations = $appellation->getParent()->getAppellationsSorted();
        $lieux = $lieu->getParent()->getLieuxSorted();
        while($previous = array_pop($lieux)) {
            if($previous->getHash() == $lieu->getHash() && count($lieux) > 0){
                return array_pop($lieux);
            }                
        }
        while($previous = array_pop($appellations)) {
            if($previous->getHash() == $appellation->getHash() && count($appellations) > 0){
                return array_pop($appellations);
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
    
    public function getConfigurationCampagne() {
        return acCouchdbManager::getClient('Configuration')->retrieveConfiguration(substr($this->campagne,0,4));
    }
    
    public function getCepage($hash) {
        return $this->get($hash);
    }
    
    public function getTotalAOC() {
        $total = 0;
        foreach ($this->declaration->getAppellationsSorted() as $hash => $appellation) {
            if(!preg_match('/^appellation_VINTABLE/', $hash))
                    $total += ($appellation->getTotalStock())? $appellation->getTotalStock() : 0;
        }
        return $total;
    }
    
    public function getTotalVinSansIg() {
        foreach ($this->declaration->getAppellationsSorted() as $hash => $appellation) {
            if(preg_match('/^appellation_VINTABLE/', $hash))
                    return ($appellation->getTotalStock())? ($appellation->getTotalStock() - $this->getTotalMousseuxSansIg()) : 0;
        }
        return 0;
    }
    
    public function getTotalMousseuxSansIg() {
        foreach ($this->declaration->getAppellationsSorted() as $hash => $appellation) {
            if(preg_match('/^appellation_VINTABLE/', $hash)){
                if(!$appellation->exist('mention')) return 0;
                if(!$appellation->mention->exist('lieu')) return 0;
                if(!$appellation->mention->lieu->exist('couleur')) return 0;
                foreach ($appellation->mention->lieu->couleur->getCepages() as $hash_c => $cepage){
                    if($hash_c == 'cepage_MS')
                        return ($cepage->detail[0]->volume_normal)? $cepage->detail[0]->volume_normal : 0;
                }
            }
        }
        return 0;
    }
    
    
    public function isDsPrincipale() {
        return $this->getLieuStockage() == '001';
    }


    public function updateAutre($rebeches = 0,$dplc = 0,$lies = 0,$mouts = 0){
        if($this->isDsPrincipale()){
            $this->rebeches += $rebeches;
            $this->dplc += $dplc;
            $this->lies += $lies;
            $this->mouts += $mouts;
        }
    }
    
    public function getUsagesIndustriels() {
        return $this->lies + $this->dplc;
    }
    
    public function updateEtape($etape_rail) {
         $nb_lieux = DSCivaClient::getInstance()->getNbDS($this);
         if($this->isDsPrincipale() && ($etape_rail > $this->num_etape)){
                $this->add('num_etape', $etape_rail);
                $this->save();
                return $ds;
         }
         if(!$this->isDsPrincipale() && $etape_rail == 3){
                $ds = DSCivaClient::getInstance()->getDSPrincipaleByDs($this);
                if($ds->num_etape < $etape_rail + $this->getLieuStockage() - 1){
                    $ds->add('num_etape', $etape_rail + $this->getLieuStockage() - 1);
                    $ds->save();
                }
                return $ds;
         }
         if($etape_rail > 3){
                $ds = DSCivaClient::getInstance()->getDSPrincipaleByDs($this);
                if($ds->num_etape < $etape_rail + $nb_lieux - 1){
                    $ds->add('num_etape', $etape_rail + $nb_lieux - 1);
                    $ds->save();
                }
                return $ds;
         }  
    }
    
    public function addVolumesWithHash($hash,$lieu,$vol_normal,$vol_vt,$vol_sgn,$sum = false) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $cepage = $this->getOrAdd($hash);
        if(!$cepage) return "NO_CEPAGE";
        if($lieu == "") $lieu = null;
        if(!$cepage->checkNoVTSGNImport($vol_vt,$vol_sgn)) return "NO_VTSGN_AND_VTORSGN";
        $detail = $cepage->addVolumes($lieu,$vol_normal,$vol_vt,$vol_sgn,$sum);
        return $detail;
    }
    
}

