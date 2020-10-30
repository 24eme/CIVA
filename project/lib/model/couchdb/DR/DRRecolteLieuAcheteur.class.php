<?php

class DRRecolteLieuAcheteur extends BaseDRRecolteLieuAcheteur
{
  private $acheteur = null;

  public function getNoeud() {
      return $this->getParent()->getParent()->getParent();
  }

  public function getVolume() {
    return $this->getNoeud()->getVolumeAcheteur($this->getKey(), $this->type_acheteur);
  }
  public function getNom() {
    if ($v = $this->_get('nom'))
      return $v;
    $v = $this->getAcheteurFromCVI()->getNom();
    $this->nom = $v;
    return $v;
  }
  public function getCommune() {
    if ($v = $this->_get('commune'))
      return $v;
    $v = $this->getAcheteurFromCVI()->getCommune();
    $this->commune = $v;
    return $v;
  }
  public function getCVI() {
    return $this->getKey();
  }
  private function getAcheteurFromCVI() {
    if (!$this->acheteur) {
      $this->acheteur = EtablissementClient::getInstance()->findByCvi($this->getKey());
    }
    if (!$this->acheteur) {
        $this->acheteur = new Etablissement();
    }
    return $this->acheteur;
  }

  protected function update($params = array()) {
    parent::update($params);
    $this->getNom();
    $this->getCommune();
    $this->synchronizeRecolteAcheteur();
  }

  protected function synchronizeRecolteAcheteur() {
        if($this->getNoeud()->getMention()->getKey() != "mention") {
            $this->getDocument()->add('acheteurs')->addAppellationTypeCVI($this->getNoeud()->getMention()->getKey(), $this->type_acheteur, $this->getCVI());
        }

        $this->getDocument()->add('acheteurs')->addAppellationTypeCVI($this->getNoeud()->getAppellation()->getKey(), $this->type_acheteur, $this->getCVI());

  }
}
