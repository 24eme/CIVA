<?php

class DRRecolteAppellationLieu extends BaseDRRecolteAppellationLieu {
  public function getLibelle() {
    return ConfigurationClient::getConfiguration()->get($this->getHash())->getLibelle();
  }
}
