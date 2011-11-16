<?php
class Acheteur extends BaseAcheteur {
  const ACHETEUR_COOPERATIVE = 'Cooperative';
  const ACHETEUR_NEGOCIANT = 'Negociant';
  const ACHETEUR_NEGOCAVE = 'NegoCave';
  public function getAcheteurDRType() {
    if ($this->qualite == self::ACHETEUR_NEGOCIANT)
      return "negoces";
    return "cooperatives";
  }
}