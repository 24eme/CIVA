<?php

class sfCouchdbJsonDefinitionFieldArrayCollection extends sfCouchdbJsonDefinitionFieldCollection {
    public function __construct($name, $required, $model, $hash, $collection_class = 'sfCouchdbJson') {
        parent::__construct($name, $required, $model, $hash, $collection_class);
    }

    public function getJsonObject() {
        $json_collection = parent::getJsonObject(null);
        $json_collection->setIsArray(true);
        return $json_collection;
    }
}