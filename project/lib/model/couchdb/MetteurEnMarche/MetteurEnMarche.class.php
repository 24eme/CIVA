<?php
class MetteurEnMarche extends BaseMetteurEnMarche {
    public function __toString() {
        return $this->getNom() . " - "."Metteur en marché";
    }

    public function getAcheteur() {

        return acCouchdbManager::getClient('Acheteur')->retrieveByCvi($this->cvi_acheteur);
    }

    public function getIdentifiant() {

        return $this->civaba;
    }
    
    public function hasCvi() {
    	if ($this->cvi_acheteur) {
    		return true;
    	}
    	return false;
    }
}