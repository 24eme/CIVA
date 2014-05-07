<?php

abstract class BaseFlag extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Flag';
    }
    
}