<?php

abstract class BaseMessages extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Messages';
    }
}
