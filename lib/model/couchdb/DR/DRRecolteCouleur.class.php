<?php

class DRRecolteCouleur extends BaseDRRecolteCouleur {

    public function getConfig() {
      return $this->getCouchdbDocument()->getConfigurationCampagne()->get($this->getHash());
    }

    public function getLieu() {
      return $this->getParent();
    }

    public function getAppellation() {
      return $this->getLieu()->getAppellation();
    }

    public function getCepages() {
      return $this->filter('^cepage');
    }

}
