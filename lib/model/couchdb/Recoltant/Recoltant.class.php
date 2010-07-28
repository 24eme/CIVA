<?php
class Recoltant extends BaseRecoltant {
    public function getDeclaration($campagne) {
         return sfCouchdbManager::getClient('DR')->retrieveByCampagneAndCvi($this->cvi, $campagne);
    }

    public function getDeclarationArchivesCampagne($campagne) {
         return sfCouchdbManager::getClient('DR')->getArchivesCampagnes($this->cvi, $campagne);
    }
}