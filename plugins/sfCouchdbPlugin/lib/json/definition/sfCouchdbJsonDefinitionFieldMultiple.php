<?php

class sfCouchdbJsonDefinitionFieldMultiple extends sfCouchdbJsonDefinitionField {
    public function __construct($type = self::TYPE_STRING) {
        parent::__construct('*', $type);
        $this->is_multiple = true;
    }
}