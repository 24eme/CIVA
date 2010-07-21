<?php

class sfCouchdbJsonDefinitionParser {

    public static function parse($data) {
        return (self::parseDefinition(new sfCouchdbJsonDefinition(), self::getValueRequired($data, 'definition', 'global')));
    }

    public static function searchDefinitionByClass($data, $class) {
        $data_definition = self::getValueRequired($data, 'definition');
        if (self::getValue($data_definition, 'free') === true) {
            return false;
        } else {
            $data_fields = self::getValue($data_definition, 'fields');
            foreach($data_fields as $key => $data_field) {
                $type = self::getValue($data_field, 'type', 'string');
                if ($type == sfCouchdbJsonDefinitionField::TYPE_COLLECTION || $type == sfCouchdbJsonDefinitionField::TYPE_ARRAY_COLLECTION) {
                    if (self::getValue($data_field, 'class') == $class) {
                        return $data_field;
                    } else {
                        $search = self::searchDefinitionByClass($data_field, $class);
                        if($search) {
                            return $search;
                        }
                    }
                }
            }
        }

        return false;
    }

    protected static function parseDefinition($definition, $data_definition) {
        if (self::getValue($data_definition, 'free') === true) {
            $definition->setIsFree(true);
        } else {
            self::parseFields($definition, self::getValueRequired($data_definition, 'fields'));
        }
        return $definition;
    }

    protected static function parseFields($definition, $data_fields) {
        foreach($data_fields as $key => $data_field) {
            self::parseField($definition, $key, $data_field);
        }

        return $definition;
    }

    protected static function parseField($definition, $key, $data_field) {
        $type = self::getValue($data_field, 'type', 'string');
        $multiple = ($key == '*');
        if (in_array($type, array(sfCouchdbJsonDefinitionField::TYPE_STRING,
                                  sfCouchdbJsonDefinitionField::TYPE_ANYONE,
                                  sfCouchdbJsonDefinitionField::TYPE_INTEGER,
                                  sfCouchdbJsonDefinitionField::TYPE_FLOAT))) {
            if (!$multiple) {
                $definition->add(new sfCouchdbJsonDefinitionField($key, $type));
            } else {
                $definition->add(new sfCouchdbJsonDefinitionFieldMultiple($type));
            }
        } elseif ($type == sfCouchdbJsonDefinitionField::TYPE_COLLECTION) {
            if (!$multiple) {
                self::parseDefinition(
                    $definition->add(new sfCouchdbJsonDefinitionFieldCollection($key, self::getValue($data_field, 'class', 'sfCouchdbJson')))
                        ->getDefinition(),
                    self::getValueRequired($data_field, 'definition', $key)
                );
            } else {
                self::parseDefinition(
                    $definition->add(new sfCouchdbJsonDefinitionFieldMultipleCollection(self::getValue($data_field, 'class', 'sfCouchdbJson')))
                        ->getDefinition(),
                    self::getValueRequired($data_field, 'definition', $key)
                );
            }
            
        } elseif ($type == sfCouchdbJsonDefinitionField::TYPE_ARRAY_COLLECTION) {
            if (!$multiple) {
                self::parseDefinition(
                    $definition->add(new sfCouchdbJsonDefinitionFieldArrayCollection($key, self::getValue($data_field, 'class', 'sfCouchdbJson')))
                        ->getDefinition(),
                    self::getValueRequired($data_field, 'definition', $key)
                );
            } else {
                self::parseDefinition(
                    $definition->add(new sfCouchdbJsonDefinitionFieldMultipleArrayCollection(self::getValue($data_field, 'class', 'sfCouchdbJson')))
                        ->getDefinition(),
                    self::getValueRequired($data_field, 'definition', $key)
                );
            }
        } else {
            throw new sfCouchdbException(sprintf("Parser Type doesn't exit : %s", $type));
        }
        return $definition;
    }

    protected static function getValue($array, $value, $default = null) {
        if (isset($array[$value])) {
            return $array[$value];
        } else {
            return $default;
        }
    }

    protected static function getValueRequired($array, $value, $infos = null)  {
        if (is_null(self::getValue($array, $value))) {
            throw new sfCouchdbException(sprintf('parse error : %s (%s)', $value, $infos));
        } else {
            return self::getValue($array, $value);
        }
    }
    
}
