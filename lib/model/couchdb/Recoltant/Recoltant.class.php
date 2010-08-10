<?php
class Recoltant extends BaseRecoltant {
    public function getDeclaration($campagne) {
      return sfCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->cvi, $campagne);
    }

    public function getDeclarationArchivesCampagne($campagne) {
      return sfCouchdbManager::getClient('DR')->getArchivesCampagnes($this->cvi, $campagne);
    }

    public function getAdresse() {
      return $this->get('siege')->get('adresse');
    }
    public function getCodePostal() {
      return $this->get('siege')->get('code_postal');
    }
    public function getCommune() {
      return $this->get('siege')->get('commune');
    }
    public function setAdresse($a) {
      return $this->get('siege')->set('adresse', $a);
    }
    public function setCodePostal($c) {
      return $this->get('siege')->set('code_postal', $c);
    }
    public function setCommune($c) {
      return $this->get('siege')->set('commune', $c);
    }
}