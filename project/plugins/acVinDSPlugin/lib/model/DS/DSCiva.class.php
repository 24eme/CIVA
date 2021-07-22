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

        if(!$tiers->getLieuxStockage(true, $this->identifiant)) {
            throw new sfException(sprintf("Aucun lieu de stockage n'existe dans l'etablissement de cvi %s ", $this->identifiant));
        }

        if(!isset($tiers->lieux_stockage[$num_lieu])) {
            throw new sfException(sprintf("Le lieu de stockage %s n'existe pas dans l'etablissement de cvi %s ", $num_lieu, $this->identifiant));
        }

        $this->set('stockage', $tiers->lieux_stockage[$num_lieu]);
    }

    public function storeDeclarant() {
        parent::storeDeclarant();

        $tiers = $this->getEtablissement();
        $this->declaration_commune = $tiers->declaration_commune;
        $this->declaration_insee = $tiers->declaration_insee;

        $this->declarant->email = $tiers->getEmailTeledeclaration();

        if($tiers->exist('civaba') && $tiers->civaba){
            $this->add('civaba', $tiers->civaba);
        }

        $this->declarant->exploitant->sexe = $tiers->exploitant->civilite;
        $this->declarant->exploitant->nom = $tiers->exploitant->nom;
        $this->declarant->exploitant->adresse = $tiers->exploitant->adresse;
        $this->declarant->exploitant->code_postal = $tiers->exploitant->code_postal;
        $this->declarant->exploitant->commune = $tiers->exploitant->commune;
        //$this->declarant->exploitant->date_naissance = $tiers->exploitant->date_naissance;
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
        return DRClient::getInstance()->retrieveByCampagneAndCvi($this->identifiant, CurrentClient::getCurrent()->campagne);
    }

    public function isDecembre() {

        return substr($this->periode, 4, 2) == "12";
    }

    public function updateProduitsFromLastDs() {
        $ds = $this->getLastDS();
        if ($ds) {
            $this->updateProduitsFromDS($ds);
        }
    }

    public function updateProduitsFromLastDr(){
        if ($this->isTypeDsPropriete()) {
            $dr = $this->getLastDR();
            if($dr){
                $this->drm_origine = $dr->_id;
                $this->addDetailsFromDRPropriete($dr);
            }
        }
        if($this->isTypeDsNegoce()){
            $this->addDetailsFromDRNegoce();
        }
    }

    protected function updateProduitsFromDS($ds) {
        $this->drm_origine = $ds->_id;
        $this->addDetailsFromDS($ds);
    }

    public function isTypeDsPropriete(){
        return ($this->exist('type_ds') && ($this->get('type_ds') == DSCivaClient::TYPE_DS_PROPRIETE));
    }

    public function isTypeDsNegoce(){
        return ($this->exist('type_ds') && ($this->get('type_ds') == DSCivaClient::TYPE_DS_NEGOCE));
    }

    public function getTypeDs() {
        if($this->exist('type_ds')) {

            return $this->_get('type_ds');
        }

        return DSCivaClient::TYPE_DS_PROPRIETE;
    }

    public function getLastDS() {
        return DSCivaClient::getInstance()->getLastDs($this);
    }

    public function addNoeud($hash) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash = preg_replace("/(mentionVT|mentionSGN)/", "mention", $hash);
        $noeud = $this->getOrAdd($hash);
        $config = $noeud->getConfig();
        $noeud->libelle = $config->getLibelle();
        if($noeud instanceof DSCepage && count($config->getParentNode()->getChildrenNode()) <= 1 && !$config->hasLieuEditable()) {
            $this->addDetail($hash);
        }

        if($config->isAutoDs()) {
            foreach($config->getProduits() as $item) {
                if(!$item->isForDS()) {
                    continue;
                }
                $this->addDetail(HashMapper::inverse($item->getHash()));
            }
        }

        if($noeud instanceof DSAppellation) {
            $this->addNoeud(HashMapper::inverse($config->getChildrenNode()->getFirst()->getHash()));
        } elseif(count($config->getChildrenNode()) == 1) {
            $this->addNoeud(HashMapper::inverse($config->getChildrenNode()->getFirst()->getHash()));
        }

        return $noeud;
    }

    public function addAppellation($hash) {

        return $this->addNoeud($hash);
    }

    public function addLieu($hash) {

        return $this->addNoeud($hash);
    }

    public function addDetailsFromDRPropriete($dr) {
        foreach ($dr->getProduitsDetails() as $detail) {
            if(!$detail->cave_particuliere) {

                continue;
            }

            $this->addDetail($detail->getCepage()->getHash(), $detail->lieu);
        }

        if($dr->recolte->getVciCaveParticuliere() > 0) {
            $this->addAppellation("declaration/certification/genreVCI");
        }
    }

    public function addDetailsFromDRNegoce()
    {
        $campagne = CurrentClient::getCurrent()->campagne;
        $cvi_acheteur = $this->getEtablissement()->getCvi();
        if(!$cvi_acheteur) {
            return;
        }
        $drs = DRClient::getInstance()->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach ($drs as $id => $doc) {
            $dr = acCouchdbManager::getClient('DR')->find($id);
            foreach ($dr->getProduitsDetails() as $detail) {
                if(!$detail->getVolumeByAcheteur($cvi_acheteur)) {

                    continue;
                }
                $this->addDetail($detail->getCepage()->getHash(), $detail->lieu);
            }
            if($dr->recolte->getTotalDontVciVendusByCvi('negoces', $cvi_acheteur) || $dr->recolte->getTotalDontVciVendusByCvi('cooperatives', $cvi_acheteur)) {
                $this->addAppellation("declaration/certification/genreVCI");
            }
        }
    }

    public function addDetailsFromDS($ds)
    {
        foreach ($ds->declaration->getProduitsDetails() as $detail) {
            $this->addDetail($detail->getCepage()->getHash(), $detail->lieu, true);
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


    public function addProduit($hash,$fromDs = false) {
        $hash = preg_replace('/^\/recolte/','declaration', $hash);
        $hash = preg_replace("/(mentionVT|mentionSGN)/", "mention", $hash);
        $hash_config = HashMapper::convert($hash);

        if(!$this->getConfig()->exist($hash_config)) {
            return null;
        }

        if(!$this->getConfig()->get($hash_config)->isForDS()) {
            $this->addNoeud(HashMapper::inverse($this->getConfig()->get($hash_config)->getParentNode()->getHash()));
            return null;
        }

        $produit = $this->getOrAdd($hash);

        $config = $produit->getConfig();
        $produit->libelle = $config->getLibelle();
        $produit->getCouleur()->libelle = $produit->getConfig()->getCouleur()->libelle;
        $produit->getLieu()->libelle = $produit->getConfig()->getLieu()->libelle;
        $produit->getMention()->libelle = $produit->getConfig()->getMention()->libelle;
        $produit->getAppellation()->libelle = $produit->getConfig()->getAppellation()->libelle;
        $produit->no_vtsgn = !$config->getDocument()->exist(str_replace("/mentions/DEFAUT/", "/mentions/VT/", $config->getHash()));
        return $produit;
    }

    public function addDetail($hash, $lieudit = null, $fromDs = false) {
        $produit = $this->addProduit($hash, $fromDs);
        if(!$produit) {
            return;
        }
        return $produit->addDetail($lieudit);
    }

    public function getLieuStockage() {
        $matches = array();

        preg_match('/^DS-(C?[0-9]{10})-([0-9]{6})-([0-9]{3})$/', $this->_id, $matches);
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

    public function getEtablissementObject() {

        return $this->getEtablissement();
    }

    public function getDeclarantObject() {

        return $this->getEtablissement();
    }

   public function getEtablissement() {
        $etablissement = EtablissementClient::getInstance()->find($this->identifiant);
        if(!$etablissement) {

            $etablissement = EtablissementClient::getInstance()->find("ETABLISSEMENT-C".$this->civaba);
        }

        return $etablissement;
    }

    public function getConfig() {

        return ConfigurationClient::getConfiguration($this->getDateStock());
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
        if($lieu instanceof DSLieu) {
            $appellation = $lieu->getAppellation();
            $lieux = $lieu->getParent()->getLieuxSorted();
        } else {
            $lieux = array();
            $appellation = $lieu;
        }
        $appellations = $this->declaration->getAppellationsSorted();
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
        if($lieu instanceof DSLieu) {
            $appellation = $lieu->getAppellation();
            $lieux = $lieu->getParent()->getLieuxSorted();
        } else {
            $lieux = array();
            $appellation = $lieu;
        }
        $appellations = $this->declaration->getAppellationsSorted();
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
        $campagne = (int) (preg_replace("/^([0-9]{4})[0-9]{2}$/", '\1', $this->getPeriode()) - 1);
        $conf_2012 = ConfigurationClient::getConfiguration('2012');

        if($campagne <= 2012){

            return $conf_2012;
        }
        $conf = ConfigurationClient::getConfiguration($campagne);
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
            if(preg_match('/appellation_VINTABLE/', $hash)) {

                continue;
            }

            if(preg_match('/VCI/', $hash)) {

                continue;
            }

            $total += ($appellation->get($type)) ? $appellation->get($type) : 0;
        }
        return $total;
    }

    public function getTotalVinSansIg() {
        if(!$this->exist('/declaration/certification/genre/appellation_VINTABLE')) {
            return 0;
        }
        $volume = 0;
        foreach ($this->get('/declaration/certification/genre/appellation_VINTABLE')->getProduitsDetails() as $produit) {
            if(strpos($produit->getHash(), '/cepage_MS') !== false) {
                continue;
            } 
            $volume += $produit->volume_normal;
        }
        return $volume;
    }

    public function getTotalMousseuxSansIg() {
        if(!$this->exist('/declaration/certification/genre/appellation_VINTABLE')) {
            return 0;
        }
        
        return $this->get('/declaration/certification/genre/appellation_VINTABLE')->getTotalStock() - $this->getTotalVinSansIg();
    }

    public function getTotalVCI() {
        $recap = array();
        if(!$this->exist('declaration/certification')) {

            return $recap;
        }

        if(!$this->declaration->certification->getConfig()->exist('genres/VCI')) {

            return $recap;
        }
        foreach($this->declaration->certification->getConfig()->genres->get('VCI')->appellations as $appellation) {
            $recap["VCI ".$appellation->getLibelle()] = 0;
        }

        if(!$this->exist('declaration/certification/genreVCI')) {

            return $recap;
        }
        foreach($this->declaration->certification->genreVCI->getAppellations() as $appellation) {
            $recap["VCI ".$appellation->getLibelle()] += $appellation->total_stock;
        }

        return $recap;
    }

    public function getTotalVCIVolume() {
        $volumeTotal = null;

        foreach($this->getTotalVCI() as $volume) {
            $volumeTotal += $volume;
        }

        return $volumeTotal;
    }

    public function isDsPrincipale() {
        if($this->exist('ds_principale')){
            return $this->ds_principale;
        }
        $is_new_system_dsPrincipal = false;
        $dss = DSCivaClient::getInstance()->findDssByDs($this);
        foreach ($dss as $current_ds) {
            if($current_ds->exist('ds_principale')){
                $is_new_system_dsPrincipal = true;
                break;
            }
        }
        if($is_new_system_dsPrincipal){

            return $this->exist('ds_principale') && $this->ds_principale;
        }

        $ds_principale = null;
        foreach($dss as $ds) {
            $ds_principale = $ds;
            break;
        }

        if($ds_principale) {
            return $this->_id == $ds_principale->_id;
        }


        return $this->getLieuStockage() == $this->getEtablissement()->getLieuStockagePrincipal(false, $this->getIdentifiant())->getNumeroIncremental();
    }

    public function isFirstDs(){
        return $this->getLieuStockage() == DSCivaClient::getInstance()->getFirstDSByDs($this)->getLieuStockage();
    }

    public function isSupprimable() {

        return !$this->isDsPrincipale() && $this->hasNoAppellation();
    }

    public function updateAutre($rebeches = 0,$dplc = 0,$dplc_rouge = 0,$lies = 0,$mouts = 0){
        if($this->isDsPrincipale()){
            $this->rebeches += $rebeches;
            $this->dplc += $dplc;
            $this->lies += $lies;
            $this->mouts += $mouts;
            if (!$this->exist('dplc_rouge')) {
                $this->add('dplc_rouge');
            }
            $this->dplc_rouge += $dplc_rouge;
        }
    }

    public function getUsagesIndustriels() {
        $ui = $this->lies + $this->dplc;
        if ($this->exist("dplc_rouge")) {
            $ui += $this->dplc_rouge;
        }
        return $ui;
    }

    public function getNumEtapeAbsolu() {
        $nb_lieux = DSCivaClient::getInstance()->getNbDS($this);
        return $this->num_etape - $nb_lieux + 1;
    }

    public function updateEtape($etape_rail, $current_ds = null, $new_hash = "",$ds_p = null) {
         $courant_stock = $this->getMajCourantStock($current_ds,$new_hash,$ds_p);

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

    private function getMajCourantStock($current_ds = null, $new_hash = "", $ds_p = null){
       if(!$current_ds) return null;
       if($ds_p){
           $ds_principale = $ds_p;
           $courant_stock =  $ds_p->_id."-".$new_hash;
       }else{
           $ds_principale = DSCivaClient::getInstance()->getDSPrincipaleByDs($this);
           $courant_stock = $current_ds->_id."-".$new_hash;
       }
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
        if($lieu == "" || preg_match('/appellation_GRDCRU/', $hash)){
            $lieu = null;
        }
        if(!$cepage->checkNoVTSGNImport($vol_vt,$vol_sgn)) return "NO_VTSGN_AND_VTORSGN";
        $detail = $cepage->addVolumes($lieu,$vol_normal,$vol_vt,$vol_sgn,$sum);
        return $detail;
    }

    public function hasAOC() {
        foreach($this->declaration->getAppellationsSorted() as $appellation) {
            if(preg_match('/appellation_VINTABLE/',$appellation->getKey())) {
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

        $ds_validee = ($this->exist('validee') && $this->validee);
        if(!$ds_validee){
            $this->add('validee', $date);
        }
        $this->add('modifiee', $date);

        if ($compteValidateurId) {
            $this->addValidation($compteValidateurId, date('Y-m-d'));
        }
        if($this->isDateDepotMairie()){
            $this->add('validee', $this->date_depot_mairie);
            if(!$ds_validee){
                $this->add('modifiee', $this->date_depot_mairie);
            }
        }
        return $this;
    }

    public function hasMouts() {
        if (!$this->isDsPrincipale()) {
            return false;
        }
        if($this->exist('mouts') && $this->mouts > 0){
            return true;
        }
        return false;
    }

    public function hasRebeches() {
        if (!$this->declaration->hasCremant()) {
            return false;
        }
        return ($this->exist('rebeches') && $this->rebeches > 0);
    }

    public function hasRebechesForXML() {
        return ($this->isDsPrincipale() && $this->exist('rebeches') && $this->rebeches > 0);
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

    public function isAjoutLieuxDeStockage()
    {
        return $this->getEtablissement()->isAjoutLieuxDeStockage();
    }

    public function isArchivageCanBeSet() {

        return $this->isValidee();
    }

    public function isDateDepotMairie(){
      return $this->exist('date_depot_mairie') && !is_null($this->date_depot_mairie);
    }

    public function getDateDepotMairieFr() {
       return Date::francizeDate($this->date_depot_mairie);
    }

    public function getDateValidationFr() {
        return $this->getDateFromUtilisateurs('validation');
    }

    public function getDateEditionFr() {
       return $this->getDateFromUtilisateurs('edition');
    }

    private function getDateFromUtilisateurs($editOrValid)
    {
        $date = substr($this->periode, 0,4).'-'.substr($this->periode, 4).'-31';
        if($this->exist('utilisateurs') && $this->utilisateurs->exist($editOrValid)){
            $node = $this->utilisateurs->$editOrValid->toSimpleFields();
            if(count($node)){
                $date = $node[key($node)];
            }
        }
       return Date::francizeDate($date);
    }

    function setDepotmairie($date_iso) {
        if($this->modifiee == $this->validee){
            $this->modifiee = $date_iso;
        }
        $this->validee = $date_iso;
        $this->add('date_depot_mairie',$date_iso);
    }

    public function setDateStock($date_stock) {
        $this->date_echeance = Date::getIsoDateFinDeMoisISO($date_stock, 1);
        $this->periode = DSCivaClient::getInstance()->buildPeriode($date_stock, $this->get('type_ds'));
        return $this->_set('date_stock', $date_stock);
    }

    public function getDRMEdiProduitRows(DRMGenerateCSV $drmGenerateCSV){
      $lignesEdi = "";
      $isFirstDRMCampagne = ($drmGenerateCSV->getPeriode()-1 == $this->getPeriode());

      foreach ($this->getProduits() as $hashProduit => $produit) {
        $lignesEdi.= $drmGenerateCSV->createRowStockProduitFromDS($produit,$isFirstDRMCampagne);
      }

      if($this->exist('lies') && $this->getLies()) {
         $lignesEdi.= $drmGenerateCSV->createRowStockProduitAutreFromDS("Lies et Bourbes", $this->getLies());
      }

      if($this->exist('rebeches') && $this->getRebeches()) {
          $lignesEdi.= $drmGenerateCSV->createRowStockProduitAutreFromDS("Rebêches ", $this->getRebeches());
      }

     if($this->exist('dplc') && $this->getDplc()) {
       $lignesEdi.= $drmGenerateCSV->createRowStockProduitAutreFromDS("DRA/DPLC Blanc", $this->getDplc());
     }

     if($this->exist('dplc_rouge') && $this->getDplcRouge()) {
       $lignesEdi.= $drmGenerateCSV->createRowStockProduitAutreFromDS("DRA/DPLC Rouge", $this->getDplcRouge());
     }

      return $lignesEdi;
    }

    public function getDRMEdiMouvementRows(DRMGenerateCSV $drmGenerateCSV){
      return "";
    }


}
