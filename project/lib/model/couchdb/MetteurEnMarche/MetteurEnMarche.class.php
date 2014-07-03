<?php
class MetteurEnMarche extends BaseMetteurEnMarche {
    public function __toString() {
        return $this->getNom() . " - "."Metteur en marché";
    }

    public function getAcheteur() {

        return acCouchdbManager::getClient('Acheteur')->findByCvi($this->cvi);
    }

    public function getIdentifiant() {

        return 'C'.$this->civaba;
    }

    public function isDeclarantContratForSignature() {

        return !$this->hasAcheteur();
    }

    public function isDeclarantContratForResponsable() {

        return true;
    }

    public function isDeclarantStockPropriete() {

        return false;
    }

    public function isDeclarantGamma() {

        return ($this->no_accises) ? true : false;
    }

    public function hasAcheteur() {
        if(!$this->hasCvi()) {
            return false;            
        }

        $acheteur = $this->getCviObject(); 

        if(!$acheteur) {

            return false;
        }

        return true;
    }
    
    public function hasCvi() {
    	if ($this->cvi) {
    		return true;
    	}
    	return false;
    }

    public function getCviObject() {

        return $this->getAcheteur();
    }

    public function getCvi() {
        if(!$this->_get('cvi') && $this->exist('cvi_acheteur')) {

            return $this->_get('cvi_acheteur');
        }

        return $this->_get('cvi');
    }

}