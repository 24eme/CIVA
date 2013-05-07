<?php

abstract class BaseDR extends acCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'DR';
    }
}
