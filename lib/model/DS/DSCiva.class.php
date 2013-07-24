<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DSCiva
 * @author mathurin
 */
class DSCiva extends DS implements IUtilisateursDocument {
    
    protected $utilisateurs_document = null; 
    
    public function  __construct() {
        parent::__construct();         
        $this->archivage_document = new ArchivageDocument($this);
        $this->utilisateurs_document = new UtilisateursDocument($this);
    }
    
    public function constructId() {
        if ($this->statut == null) {
            $this->statut = DSClient::STATUT_A_SAISIR;
        }

        $this->set('_id', DSClient::getInstance()->buildId($this->identifiant, $this->periode, $this->getEtablissement()->getLieuStockagePrincipale()->getNumeroIncremental()));
    }


    public function storeStockage() {
        $tiers = $this->getEtablissement();
        if(!$tiers){
            throw new sfException(sprintf("L'etablissement de cvi %s n'existe pas", $this->identifiant));
        }
        $num_lieu = $this->identifiant.$this->getLieuStockage();
        if(!$tiers->exist('lieux_stockage')) {
            throw new sfException(sprintf("Aucun lieu de stockage n'existe dans l'etablissement de cvi %s ", $this->identifiant));
        }
        if(!$tiers->lieux_stockage->exist($num_lieu)) {
            throw new sfException(sprintf("Le lieu de stockage %s n'existe pas dans l'etablissement de cvi %s ", $num_lieu, $this->identifiant));
        }

        $this->set('stockage', $tiers->lieux_stockage->get($num_lieu));
    }

    public function storeDeclarant() {
        parent::storeDeclarant();

        $tiers = $this->getEtablissement();

        $this->declaration_commune = $tiers->declaration_commune;
        $this->declaration_insee = $tiers->declaration_insee;

        if(!$this->declarant->email) {
            $this->declarant->email = $tiers->getCompteEmail();
        }

        $this->declarant->exploitant->sexe = $tiers->exploitant->sexe;
        $this->declarant->exploitant->nom = $tiers->exploitant->nom;
        $this->declarant->exploitant->adresse = $tiers->exploitant->adresse;
        $this->declarant->exploitant->code_postal = $tiers->exploitant->code_postal;
        $this->declarant->exploitant->commune = $tiers->exploitant->commune;
        $this->declarant->exploitant->date_naissance = $tiers->exploitant->date_naissance;
        $this->declarant->exploitant->telephone = $tiers->exploitant->telephone;
    }

    public function storeInfos() {
        $this->storeStockage();
        $this->storeDeclarant();
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

    public function getLastDS() {

        return null;
    }

    public function addNoeud($hash) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $noeud = $this->getOrAdd($hash);
        $config = $noeud->getConfig();
        $noeud->libelle = $config->getLibelle();

        if($noeud instanceof DSCepage && !$config->getParent()->hasManyNoeuds() && !$config->hasLieuEditable()) {
            $this->addDetail($hash);
        }
        
        if($config->isAutoDs()) {
            foreach($config->getProduitsFilter(ConfigurationAbstract::TYPE_DECLARATION_DS) as $item) {
                $this->addDetail($item->getHash());
            }
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

    public function addDetailsFromDR($dr) {
        foreach ($dr->getProduitsDetails() as $detail) {
            if(!$detail->cave_particuliere) {

                continue;
            }
            $this->addDetail($detail->getCepage()->getHash(), $detail->lieu);
        }
    }
    
     protected function preSave() {
        $this->archivage_document->preSave();
    }
    
    public function save($compteEditeurId = null) {
        
        if($compteEditeurId){
            $this->addEdition($compteEditeurId, date('Y-m-d'));
        }
        parent::save();
    }


    public function addProduit($hash) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash_config = preg_replace('/^declaration/','recolte', $hash);
        if(!$this->getConfig()->get($hash_config)->isForDS()) {
            $this->addNoeud($this->getConfig()->get($hash_config)->getParent()->getHash());
            return null;
        }
        
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
        $produit = $this->addProduit($hash);
        if(!$produit) {
            return;
        }
        return $produit->addDetail($lieudit);
    }

    protected function updateProduitsFromDR($dr) {     
        $this->drm_origine = $dr->_id;     
        $this->addDetailsFromDR($dr);              
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
        return acCouchdbManager::getClient('_Tiers')->retrieveByCvi($this->identifiant);
    }

    public function getConfig() {
        return ConfigurationClient::getConfiguration();
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
        $campagne = substr($this->campagne,0,4);
        $conf_2012 = acCouchdbManager::getClient('Configuration')->retrieveConfiguration('2012');
        if($campagne <= '2012'){
            return $conf_2012;
        }        
        $conf = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($campagne);
        return ($conf)? $conf : $conf_2012;
     }

    
    public function getCepage($hash) {
        return $this->get($hash);
    }
    
    public function getTotalAOC() {
        
        return $this->getTotalAOCByType('total_stock');
    }

    public function getTotalAOCByType($type) {
        $total = 0;
        foreach ($this->declaration->getAppellationsSorted() as $hash => $appellation) {
            if(preg_match('/^appellation_VINTABLE/', $hash)) {
                
                continue;        
            }

            $total += ($appellation->get($type)) ? $appellation->get($type) : 0;
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
        
        return $this->getLieuStockage() == $this->getEtablissement()->getLieuStockagePrincipal()->getNumeroIncremental();
    }

    public function isSupprimable() {

        return !$this->isDsPrincipale() && $this->hasNoAppellation();
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

    public function getNumEtapeAbsolu() {
        $nb_lieux = DSCivaClient::getInstance()->getNbDS($this);
        return $this->num_etape - $nb_lieux + 1;
    }
    
    public function updateEtape($etape_rail, $current_ds = null, $new_hash = "") {
         $courant_stock = $this->getMajCourantStock($current_ds,$new_hash); 
         
       
         if($this->isDsPrincipale()){
            if($courant_stock){
                    $this->add('courant_stock', $courant_stock);
            }      
             if($etape_rail > $this->num_etape){             
                $this->add('num_etape', $etape_rail);                
             }
             return $this;
         }else{
            $ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($this);
            if($courant_stock){
                    $ds_principale->add('courant_stock', $courant_stock);
                    $ds_principale->save();
                    return $ds_principale;
            }  
         } 
    }
    
    private function getMajCourantStock($current_ds = null, $new_hash = ""){
       if(!$current_ds) return null;
       $courant_stock = $current_ds->_id."-".$new_hash;
       
       $ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($this);
       if(!$ds_principale->exist('courant_stock')) return $courant_stock;
       
       $old_courant_stock = $ds_principale->get('courant_stock');
       $old_lieu_stockage = preg_replace('/^DS-[0-9]{10}-[0-9]{6}-([0-9]{3})[A-Za-z0-9\_\-\/]*/', '$1', $old_courant_stock);
       
       $old_hash_lieu = preg_replace('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})[-]?(\/[A-Za-z0-9\_\-\/]*)$/', '$2', $old_courant_stock);
       
       $old_hash_lieu_exist = preg_match('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})-(\/[A-Za-z0-9\_\-\/]+)$/',$old_courant_stock);
       
       if($old_lieu_stockage > $current_ds->getLieuStockage()) return null;
       
       if($new_hash=="") return $current_ds->_id;
       
       $steps = $current_ds->getLieuxHashSteps();
       if(!in_array($new_hash,$steps)) return null;
       if(!$old_hash_lieu_exist){ 
           return $courant_stock;
       }
       
       $pos_old_hash = array_search($old_hash_lieu, $steps);
       $pos_new_hash = array_search($new_hash, $steps);
       if($pos_old_hash > $pos_new_hash) return null;
       return $courant_stock;
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

    public function hasAOC() {
        foreach($this->declaration->getAppellationsSorted() as $appellation) {
            if(preg_match('/^appellation_VINTABLE$/',$appellation->getKey())) {
                continue;
            }

            return true;
        }

        return false;
    }
    
    public function hasNoAppellation() {      
        if(!$this->exist('declaration')) return true;
        if(!$this->declaration->exist('certification')) return true;
        return (count($this->declaration->getAppellationsSorted()) == 0);
    }
    
    public function isDsNeant() {
        if(!$this->isDsPrincipale()){
            throw new sfException("La DS a néant est obligatoirement la ds principale.");
        }
        return $this->exist('ds_neant') && $this->ds_neant;
    }
    
    private function restoreNodes(){
        $this->declaration->restoreNodes();
    }

    public function devalidate($juste_civa = false) {

        if($this->isDsPrincipale()){
             $this->num_etape = 1;
        }

        if(!$juste_civa) {
            $this->validee = null;
        }
        $this->modifiee = null;
        $this->restoreNodes();
        $this->restoreNodes();
        return $this;
    }
    
    public function validate($date = null, $compteValidateurId = null) {
        if($this->isDsPrincipale()){
            $this->updateEtape(6);
        }

        $this->storeInfos();

        $date = (!$date)? date("Y-m-d") : $date;
        $this->add('validee', $date);
        $this->add('modifiee', $date);
        if ($compteValidateurId) {
            $this->addValidation($compteValidateurId, date('Y-m-d'));
        }
        return $this;
    }
    
    public function isValidee(){

        return $this->isValideeCiva();
    }

    /**
     *
     * @return boolean
     */
    public function isValideeCiva() {
        if ($this->exist('modifiee')) {
            return $this->modifiee;
        }
        return false;
    }
    
    public function isValideeEtModifiee() {
        return $this->isValideeCiva() && $this->isValideeTiers() && ($this->validee != $this->modifiee);
    }

    /**
     *
     * @return boolean
     */
    public function isValideeTiers() {
        if ($this->exist('validee')) {
            return $this->validee;
        }
        return false;
    }
    
    public function getAnnee() {
        return preg_replace('/^([0-9]{4})([0-9]{2})$/', '$1', $this->periode);
    }
    
    public function nbLieuxEtape() {
        return count($this->getLieuxHashSteps());
        
    }
    
    public function getLieuxHashSteps() {
        $hash_array = array();
        foreach ($this->declaration->getAppellationsSorted() as $appellation) {
            if(!$appellation->hasManyLieu()) $hash_array[] = $appellation->getHash();
            else{
                foreach ($appellation->getLieuxSorted() as $lieu) {
                    $hash_array[] = $lieu->getHash();                    
                }
            }
        }
        return $hash_array;
    }

    public function addEdition($id_user, $date) {
        return $this->utilisateurs_document->addEdition($id_user, $date);
    }

    public function addValidation($id_user, $date) {
        return $this->utilisateurs_document->addValidation($id_user, $date);
    }
    
    public function removeValidation() {
        return $this->utilisateurs_document->removeValidation();
    }

    public function getLastEdition() {
        return $this->utilisateurs_document->getLastEdition();
    }

    public function getLastValidation() {
        return $this->utilisateurs_document->getLastValidation();        
    }

}

