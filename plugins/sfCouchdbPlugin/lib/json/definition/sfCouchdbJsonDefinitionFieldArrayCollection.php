<?php

class sfCouchdbJsonDefinitionFieldArrayCollection extends sfCouchdbJsonDefinitionFieldCollection {
    public function __construct($name, $model, $hash, $collection_class = 'sfCouchdbJson') {
        parent::__construct($name, $model, $hash, $collection_class);
    }

    public function getJsonObject($data) {
        $json_collection = parent::getJsonObject($data);
        $json_collection->setIsArray(true);
        return $json_collection;
    }
}