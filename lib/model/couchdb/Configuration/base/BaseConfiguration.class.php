<?php

abstract class BaseConfiguration extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Configuration';
    }
}
