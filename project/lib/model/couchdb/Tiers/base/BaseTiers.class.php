<?php

abstract class BaseTiers extends acCouchdbDocument {
    public function getDocumentDefinitionModel() {
        return 'Tiers';
    }
}
