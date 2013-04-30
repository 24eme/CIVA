<?php
class MetteurEnMarche extends BaseMetteurEnMarche {
    public function __toString() {
        return $this->getNom() . " - "."Metteur en marché";
    }

    public function getAcheteur() {

        return sfCouchdbManager::getClient('Acheteur')->retrieveByCvi($this->cvi_acheteur);
    }
}