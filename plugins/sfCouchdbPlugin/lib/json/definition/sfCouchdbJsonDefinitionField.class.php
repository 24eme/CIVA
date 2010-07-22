<?php

class sfCouchdbJsonDefinitionField {
    private $key;
    private $name;
    private $class;
    protected $collection = false;
    protected $collection_class = '';
    protected $is_multiple = false;
    protected $field_definition = null;
    protected $is_required = true;

    const TYPE_ANYONE = 'anyone';
    const TYPE_STRING = 'string';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_COLLECTION = 'collection';
    const TYPE_ARRAY_COLLECTION = 'array_collection';

    public function __construct($name, $type = self::TYPE_STRING, $required = true) {
        $this->key = sfInflector::underscore(sfInflector::camelize($name));
        $this->name = $name;
        if ($type == self::TYPE_STRING) {
            $this->class = 'sfCouchdbJsonFieldString';
        } elseif($type == self::TYPE_INTEGER ) {
            $this->class = 'sfCouchdbJsonFieldInteger';
        } elseif($type == self::TYPE_FLOAT ) {
            $this->class = 'sfCouchdbJsonFieldFloat';
        } elseif ($type == self::TYPE_COLLECTION) {
            $this->class = 'sfCouchdbJsonFieldCollection';
        } elseif ($type == self::TYPE_ARRAY_COLLECTION) {
            $this->class = 'sfCouchdbJsonFieldArrayCollection';
        } elseif ($type == self::TYPE_ANYONE) {
            $this->class = 'sfCouchdbJsonFieldAnyone';
        } else {
            throw new sfCouchdbException("Type doesn't exit");
        }
        $this->is_required = $required;
        return null;
    }

    public function getJsonField($data, $numeric_key, $name = null) {
            if (is_null($name)) {
                $name = $this->name;
            }
            return new $this->class($name, $this->getJsonObject($data), $numeric_key);
    }

    public function getJsonObject($data) {
        return $data;
    }

    public function getKey() {
        return $this->key;
    }

    public function getName() {
        return $this->name;
    }

    public function getFieldClass() {
        return $this->class;
    }

    public function getCollectionClass() {
        return $this->collection_class;
    }

    public function isMultiple() {
        return $this->is_multiple;
    }

    public function isRequired() {
        return $this->is_required;
    }

    public function getDefinition() {
        return $this->field_definition;
    }

    public function getDefinitionByHash($hash) {
        if (!is_null($this->field_definition)) {
            return $this->field_definition->getDefinitionByHash($hash);
        } else {
            throw new sfCouchdbException(sprintf('Hash definition does not exist : %s', $hash));
        }
    }
}