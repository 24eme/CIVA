<?php

abstract class BaseDR extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'DR';
    }
}
