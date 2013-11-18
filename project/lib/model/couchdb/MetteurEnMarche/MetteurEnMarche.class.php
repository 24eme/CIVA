<?php
class MetteurEnMarche extends BaseMetteurEnMarche {
    public function __toString() {
        return $this->getNom() . " - "."Metteur en marché";
    }

    public function getAcheteur() {

        return acCouchdbManager::getClient('Acheteur')->findByCvi($this->cvi);
    }

    public function getIdentifiant() {

        return $this->civaba;
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

    public function getCviAcheteur() {

        return $this->cvi;
    }
}