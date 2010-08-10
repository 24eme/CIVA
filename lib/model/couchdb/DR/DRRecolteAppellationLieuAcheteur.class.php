<?php

class DRRecolteAppellationLieuAcheteur extends BaseDRRecolteAppellationLieuAcheteur 
{
  public function getVolume() {
    $this->getParent()->getParent()->getVolumeAcheteur($this->getKey(), $this->type_acheteur);
  }
}