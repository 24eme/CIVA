<?php

class sfCouchdbJsonDefinitionFieldMultipleCollection extends sfCouchdbJsonDefinitionFieldCollection {
    public function __construct($model, $hash, $collection_class = 'sfCouchdbJson') {
        parent::__construct('*', false, $model, $hash, $collection_class);
        $this->is_multiple = true;
    }
}