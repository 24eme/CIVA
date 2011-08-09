<?php

abstract class BaseAcheteur extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Acheteur';
    }
}
