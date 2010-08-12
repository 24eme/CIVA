<?php

class DRRecolteAppellationLieuAcheteur extends BaseDRRecolteAppellationLieuAcheteur 
{
  private $acheteur = null;

  public function getVolume() {
    return $this->getParent()->getParent()->getVolumeAcheteur($this->getKey(), $this->type_acheteur);
  }
  public function getNom() {
    return $this->getAcheteurFromCVI()->getNom();
  }
  public function getCommune() {
    return $this->getAcheteurFromCVI()->getCommune();
  }
  public function getCVI() {
    return $this->getKey();
  }
  private function getAcheteurFromCVI() {
    if (!$this->acheteur)
      $this->acheteur = sfCouchdbManager::getClient()->retrieveDocumentById('ACHAT-'.$this->getKey());
    return $this->acheteur;
  }
}