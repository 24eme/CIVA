<?php

/**
 * Model for DS
 *
 */
abstract class DS extends BaseDS implements InterfaceDeclarantDocument, InterfaceArchivageDocument, InterfaceDSProduits {

    protected $declarant_document = null;
    protected $archivage_document = null;

    public function __construct() {
        parent::__construct();
        $this->initDocuments();
    }

    protected function initDocuments() {
        $this->declarant_document = new DeclarantDocument($this);
        $this->archivage_document = new ArchivageDocument($this);
    }
    
    public function __clone() {
        parent::__clone();
        $this->initDocuments();
    }


    public function constructId() {
        if ($this->statut == null) {
            $this->statut = DSClient::STATUT_A_SAISIR;
        }
        $this->set('_id', DSClient::getInstance()->buildId($this->identifiant, $this->periode));
    }

    public function getCampagne() {

        return $this->_get('campagne');
    }

    public function setDateStock($date_stock) {
        $this->date_echeance = Date::getIsoDateFinDeMoisISO($date_stock, 1);
        $this->periode = DSClient::getInstance()->buildPeriode($date_stock);

        return $this->_set('date_stock', $date_stock);
    }

    public function setPeriode($periode) {
        $this->campagne = DSClient::getInstance()->buildCampagne($periode);

        return $this->_set('periode', $periode);
    }

    public function getLastDS() {

        return DSClient::getInstance()->findLastByIdentifiant($this->identifiant);
    }

    public abstract function getLastDocument();

    public abstract function updateProduits();

    public abstract function addProduit($hash);
    
    public abstract function getCoordonnees();
    
    public abstract function getConfig();

    protected function updateProduitsFromDS($ds) {
        foreach ($ds->declarations as $produit) {
            if (!$produit->isActif()) {

                continue;
            }
            $produitDs = $this->addProduit($produit->produit_hash);
        }
    }

    public function isStatutValide() {
        return $this->statut === DSClient::STATUT_VALIDE;
    }

    public function isStatutASaisir() {
        return $this->statut === DSClient::STATUT_A_SAISIR;
    }

    public function updateStatut() {
        $this->statut = DSClient::STATUT_VALIDE;
    }

    protected function preSave() {
        $this->archivage_document->preSave();
        $this->updateProduits();
    }

    /*     * * DECLARANT ** */

    public function getEtablissementObject() {
        return $this->getEtablissement();
    }

    public function storeDeclarant() {
        $this->declarant_document->storeDeclarant();
    }

    /*     * * FIN DECLARANT ** */

    /*     * * ARCHIVAGE ** */

    public function getNumeroArchive() {

        return $this->_get('numero_archive');
    }

    public function isArchivageCanBeSet() {

        return $this->isStatutValide();
    }

    /*     * * FIN ARCHIVAGE ** */

    public function getDepartement() {
        if ($this->declarant->code_postal) {
            return substr($this->declarant->code_postal, 0, 2);
        }
        return null;
    }

    public abstract function getEtablissement();
    
    public function getInterpro() {
        if ($this->getEtablissement()) {
            return $this->getEtablissement()->getInterproObject();
        }
    }

    public function getMaster() {
        return $this;
    }

    public function isMaster() {
        return true;
    }

}
