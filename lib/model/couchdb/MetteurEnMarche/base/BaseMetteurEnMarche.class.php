<?php

abstract class BaseMetteurEnMarche extends _Tiers {
    public function setupDefinition() {
        $this->_definition_model = self::getDocumentDefinitionModel();
        $this->_definition_hash = '/';
    }

    public static function getDocumentDefinitionModel() {
        return 'MetteurEnMarche';
    }
}
