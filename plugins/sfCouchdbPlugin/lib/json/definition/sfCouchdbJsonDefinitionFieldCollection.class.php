<?php

class sfCouchdbJsonDefinitionFieldCollection extends sfCouchdbJsonDefinitionField {
    public function __construct($name, $required, $model, $hash, $collection_class = 'sfCouchdbJson') {
        parent::__construct($name, self::TYPE_COLLECTION, $required);
        $this->collection = true;
        $this->collection_class = $collection_class;
        $this->field_definition = new sfCouchdbJsonDefinition($model, $hash.'/'.$this->getKey());
        return $this->field_definition;
    }

    public function getJsonObject() {
        $json_collection = new $this->collection_class($this->field_definition->getModel(), $this->field_definition->getHash());
        return $json_collection;
    }
}