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

    public function isDeclarantDRAcheteur() {

        return $this->acheteur_dr == 1;
    }

    public function isDeclarantContratForSignature() {

        return true;
    }

    public function getDeclarantDS() {
        if($this->isDeclarantStockNegoce()) {

            return _TiersClient::getInstance()->findByCivaba($this->civaba);
        }

        return parent::getDeclarantDS();
    }

    public function isDeclarantStockPropriete()
    {
        if($this->categorie == self::CATEGORIE_CCV) {

            return true;
        }
        
        return parent::isDeclarantStockPropriete();
    }

    public function isDeclarantStockNegoce()
    {
        if($this->categorie == self::CATEGORIE_CCV) {

            return false;
        }
        
        return parent::isDeclarantStockNegoce();
    }

    public function isDeclarantContratForResponsable() {

        return true;
    }

    public function getIdentifiant() {

        return $this->cvi;
    }
}