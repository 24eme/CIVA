<?php
class Acheteur extends BaseAcheteur {
    const ACHETEUR_COOPERATIVE = 'Cooperative';
    const ACHETEUR_NEGOCIANT = 'Negociant';
    const ACHETEUR_NEGOCAVE = 'NegoCave';
    
    public function getAcheteurDRType() {
        if ($this->qualite == self::ACHETEUR_COOPERATIVE)
          return "cooperatives";
        return "negoces";
    }

    public function getSiret() {

        return null;
    }

    public function setSiret($value) {

        return null;
    }

    public function isDeclarantStock() {

        return $this->qualite == self::ACHETEUR_COOPERATIVE;
    }

    public function getIdentifiant() {

        return $this->cvi;
    }
}