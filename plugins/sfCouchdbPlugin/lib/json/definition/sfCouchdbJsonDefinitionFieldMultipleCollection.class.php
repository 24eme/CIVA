<?php

class sfCouchdbJsonDefinitionFieldMultipleCollection extends sfCouchdbJsonDefinitionFieldCollection {
    public function __construct($collection_class = 'sfCouchdbJson') {
        parent::__construct('*', $collection_class);
        $this->is_multiple = true;
    }
}