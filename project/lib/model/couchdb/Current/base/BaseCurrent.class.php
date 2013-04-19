<?php

abstract class BaseCurrent extends acCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Current';
    }
}
