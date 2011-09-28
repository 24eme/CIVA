<?php
class CompteProxy extends BaseCompteProxy {
    protected $_compte_reference = null;
    
    public function getCompteReferenceObject() {
        if (is_null($this->_compte_reference)) {
            $this->_compte_reference = sfCouchdbManager::getClient()->retrieveDocumentById($this->compte_reference);
            if (!$this->_compte_reference) {
                throw new sfException("Le compte référence n'existe pas");
            }
        }
        
        return $this->_compte_reference;
    }
    
    public function getNom() {
        return $this->getCompteReferenceObject()->getNom();
    }
    
    public function getTiers() {
        return $this->getCompteReferenceObject()->getTiers();
    }
    
    public function getGecos() {
        return $this->getLogin() . ',' . $this->getCompteReferenceObject()->getTiersField('no_accises', true) . ',' . $this->getCompteReferenceObject()->getTiersField('intitule') . ' ' . $this->getCompteReferenceObject()->getTiersField('nom') . ',' . $this->getCompteReferenceObject()->getTiersField('exploitant/nom', true);  
    }
    
    public function getAdresse() {
        return $this->getCompteReferenceObject()->getAdresse();
    }
    
    public function getCodePostal() {
        return $this->getCompteReferenceObject()->getCodePostal();
    }
    
    public function getCommune() {
        return $this->getCompteReferenceObject()->getCommune();
    }
}