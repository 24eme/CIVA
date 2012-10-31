<?php

abstract class BaseLS extends sfCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'LS';
    }
}
