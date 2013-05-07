<?php

abstract class BaseCSV extends acCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'CSV';
    }
}
