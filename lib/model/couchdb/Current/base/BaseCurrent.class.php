<?php

abstract class BaseCurrent extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Current';
    }
}
