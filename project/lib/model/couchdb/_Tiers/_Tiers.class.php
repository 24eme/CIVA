<?php
abstract class _Tiers extends Base_Tiers {
    
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
     * @param string $campagne
     * @return array 
     */
    public function getDsArchivesSince($campagne) {
        return acCouchdbManager::getClient('DSCiva')->retrieveDsPrincipalesByCampagneAndCvi($this->cvi, $campagne);
    }
        
    
    /**
     *
     * @param string $campagne
     * @return DR 
     */
    public function getDeclaration($campagne) {
        return acCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->cvi, $campagne);
    }
    
    /**
     *
     * @param string $campagne
     * @return DR 
     */
    public function getDs($campagne) {
        $ds = acCouchdbManager::getClient('DSCiva')->retrieveByCampagneAndCvi($this->cvi, $campagne);
        if(!$ds) return $ds;
        return DSCivaClient::getInstance()->getDSPrincipaleByDs($ds);
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

        return false;
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

    public function getLieuStockagePrincipal() {
        foreach($this->lieux_stockage as $lieu_stockage) {

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
}