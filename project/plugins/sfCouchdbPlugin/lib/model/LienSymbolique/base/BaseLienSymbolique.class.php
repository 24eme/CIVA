<?php

abstract class BaseLienSymbolique extends sfCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'LS';
    }
}
