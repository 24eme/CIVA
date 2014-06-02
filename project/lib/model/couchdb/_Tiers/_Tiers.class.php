<?php
abstract class _Tiers extends Base_Tiers {
    
    const CATEGORIE_VRP = 'VRP';
    const CATEGORIE_VRT = 'VRT';
    const CATEGORIE_VVV = 'VVV';
    
    const CATEGORIE_PN = 'PN';
    const CATEGORIE_CCV = 'CCV';
    const CATEGORIE_SIC = 'SIC';
    
    public static $array_ds_negoce = array(self::CATEGORIE_PN,self::CATEGORIE_CCV,self::CATEGORIE_SIC);
    public static $array_ds_propriete = array(self::CATEGORIE_VRP,self::CATEGORIE_VRT,self::CATEGORIE_VVV);
    
    public function isActif() {

        return $this->statut != _TiersClient::STATUT_INACTIF;
    }

    /**
     *
     * @param string $campagne
     * @return array 
     */
    public function getDeclarationsArchivesSince($campagne) {
        return acCouchdbManager::getClient('DR')->getArchivesSince($this->cvi, $campagne, 4);
    }

        /**
     *
     * @param string $periode
     * @return array 
     */
    public function getDsArchivesSince($periode) {
        return acCouchdbManager::getClient('DSCiva')->retrieveDsPrincipalesByPeriodeAndCvi($this->getIdentifiant(), $periode);
    }
        
    
    /**
     *
     * @param string $campagne
     * @return DR 
     */
    public function getDeclaration($campagne) {
        return acCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->getIdentifiant(), $campagne);
    }
    
    /**
     *
     * @param string $periode
     * @return DR 
     */
    public function getDs($periode) {
        $identifiant = $this->getIdentifiant();
        $ds = acCouchdbManager::getClient('DSCiva')->retrieveByPeriodeAndIdentifiant($identifiant, $periode);
        if(!$ds) return $ds;
        return DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);
    }
    
    /**
     *
     * @param string $campagne
     * @return DR 
     */
    public function getTypeDs() {
        if(!$this->categorie){
            return null;
        }
        if(in_array($this->categorie, self::$array_ds_negoce)){
            return DSCivaClient::TYPE_DS_NEGOCE;
        }
        
        if(in_array($this->categorie, self::$array_ds_propriete)){
            return DSCivaClient::TYPE_DS_PROPRIETE;
        }
        
        return null;
    }
    
    /**
     *
     * @return string 
     */
    public function getAdresse() {
        return $this->get('siege')->get('adresse');
    }
    
    /**
     *
     * @return string 
     */
    public function getCodePostal() {
        return $this->get('siege')->get('code_postal');
    }
    
    /**
     *
     * @return string
     */
    public function getCommune() {
        return $this->get('siege')->get('commune');
    }
    
    /**
     *
     * @param string $a
     * @return acCouchdbJsonField 
     */
    public function setAdresse($a) {
        return $this->get('siege')->set('adresse', $a);
    }
    
    /**
     *
     * @param string $c
     * @return acCouchdbJsonField 
     */
    public function setCodePostal($c) {
        return $this->get('siege')->set('code_postal', $c);
    }
    
    /**
     *
     * @param string $c
     * @return acCouchdbJsonField 
     */
    public function setCommune($c) {
        return $this->get('siege')->set('commune', $c);
    }

    public function getRaisonSociale() {
        return $this->getNom();
    }

    public function isDeclarantStock() {       
        return ($this->getTypeDs() != null);
    }
    
    public function isDeclarantStockPropriete()
    {
        return in_array($this->categorie, self::$array_ds_propriete);
    }
    
    public function isDeclarantStockNegoce() {
       return in_array($this->categorie, self::$array_ds_negoce);
    }
    
    public function isAjoutLieuxDeStockage(){
        return $this->isDeclarantStockNegoce();
    }
    
    public function storeLieuStockage($adresse,$commune,$code_postal)
    {
        $newId = 0;
        $identifiant = $this->getIdentifiant();
        if(!$this->exist('lieux_stockage')){
            $this->add('lieux_stockage');
        }
        $lieux_stockage = $this->_get('lieux_stockage');
        foreach ($lieux_stockage as $key => $value) {
            $current_id = intval(str_replace($identifiant, '', $key));
            if($current_id > $newId){
                $newId = $current_id;
            }
        }
        $newId = $identifiant.sprintf('%03d',$newId+1);
        $lieu_stockage = new stdClass();
        $lieu_stockage->numero = $newId;
        $lieu_stockage->nom = $this->nom;
        $lieu_stockage->adresse = $adresse;
        $lieu_stockage->commune = $commune;
        $lieu_stockage->code_postal = $code_postal;        
        $lieux_stockage->add($newId, $lieu_stockage);
        return $lieu_stockage;
    }

    public function isDeclarantDR() {

        return false;
    }

    public function isDeclarantDRAcheteur() {

        return false;
    }

    public function isDeclarantContrat() {

        return $this->isDeclarantContratForSignature() || $this->isDeclarantContratForResponsable();
    }

    public function isDeclarantContratForSignature() {

        return false;
    }

    public function isDeclarantContratForResponsable() {
        
        return false;
    }

    public function isDeclarantGamma() {

        return false;
    }

    public function getRegion() {
        return null;
    }

    public function getNoAccises() {
        if($this->exist('no_accises')) {

            return $this->_get('no_accises');
        }
        
        return null;
    }

    public function getCompteObject() {
        if(count($this->compte) < 1) {

            return null;
        }

        return acCouchdbManager::getClient("_Compte")->find($this->compte[0]);
    }

    public function getCompteEmail() {
        $compte = $this->getCompteObject();

        if(!$compte) {

            return null;
        }

        return $compte->email;
    }

    public function getLieuxStockage($ajoutLieuxStockage = false)
    {
        if($ajoutLieuxStockage && $this->isAjoutLieuxDeStockage() &&
                (!$this->exist('lieux_stockage') || (!count($this->_get('lieux_stockage'))))){
           $lieu_stockage = $this->storeLieuStockage($this->nom,
                    $this->siege->adresse,
                    $this->siege->commune,
                   $this->siege->code_postal);
            $this->lieux_stockage = array($lieu_stockage->numero => $lieu_stockage);
            return $this->_get('lieux_stockage');
        }
        if($this->exist('lieux_stockage')){
            return $this->_get('lieux_stockage');
        }
        return array();
    }
    
    
    public function getLieuStockagePrincipal($ajoutLieuxStockage = false) {
        foreach($this->getLieuxStockage($ajoutLieuxStockage) as $lieu_stockage) {

            return $lieu_stockage;
        }

        return null;
    }

    public function getEmailsByDroit($droit) {
        $emails = array();

        foreach($this->emails as $d => $cvis) {
            if($droit != $d) {
                continue;
            }

            foreach($cvis as $cvi) {
                if($cvi->email && !in_array($cvi->email, $emails)) {
                    $emails[] = $cvi->email;
                }
            }
        }

        return $emails;
    }

    public function getTiersExtend() {

        return false;
    }

    abstract public function getIdentifiant();
    
//    public function getIdentifiant(){
//        $identifiant = $this->cvi;
//        if(!$identifiant){
//            $identifiant = $this->civaba;
//        }
//        return $identifiant;
//    }
}