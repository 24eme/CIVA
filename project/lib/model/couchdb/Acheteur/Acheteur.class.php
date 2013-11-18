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

    public function isDeclarantStock() {

        return in_array($this->qualite, array(self::ACHETEUR_COOPERATIVE, self::ACHETEUR_NEGOCAVE));
    }

    public function getIdentifiant() {

        return $this->cvi;
    }
}