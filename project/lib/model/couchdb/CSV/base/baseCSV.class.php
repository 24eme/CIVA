<?php

abstract class BaseCSV extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'CSV';
    }
}
