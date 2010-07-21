<?php

class sfCouchdbJsonDefinitionFieldCollection extends sfCouchdbJsonDefinitionField {
    protected $field_definition = null;
    public function __construct($name, $collection_class = 'sfCouchdbJson') {
        parent::__construct($name, self::TYPE_COLLECTION);
        $this->collection = true;
        $this->collection_class = $collection_class;
        $this->field_definition = new sfCouchdbJsonDefinition();
        return $this->field_definition;
    }

    public function getDefinition() {
        return $this->field_definition;
    }
    public function getJsonObject($data) {
        $json_collection = new $this->collection_class();
        $json_collection->changeDefinition($this->field_definition);
        $json_collection->load($data);
        return $json_collection;
    }
}