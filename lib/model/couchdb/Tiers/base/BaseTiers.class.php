<?php

abstract class BaseTiers extends sfCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Tiers';
    }
}
