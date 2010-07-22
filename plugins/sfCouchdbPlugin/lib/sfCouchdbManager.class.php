<?php

class sfCouchdbManager {
    protected static $_instance;

    protected $_client;

    protected $_definition = array();
    protected $_definition_tree_hash = array();
    protected $_definition_hash = array();

    protected $_schema = null;
    
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if ( ! isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function initializeClient($dsn, $dbname) {
        self::getInstance()->_client = new sfCouchdbClient($dsn, $dbname);
	return self::getInstance()->_client;
    }

    public static function getClient() {
        return self::getInstance()->_client;
    }
    
    public static function getSchema() {
        if (is_null(self::getInstance()->_schema)) {
            self::getInstance()->_schema = sfYaml::load(sfConfig::get('sf_config_dir').'/couchdb/schema.yml');
            return self::getInstance()->_schema;
            echo '1';
        } else {
            return self::getInstance()->_schema;
        }
    }

    public static function getDefinition($model) {
        if (!isset(self::getInstance()->_definition[$model])) {
            $schema = self::getInstance()->getSchema();
            self::getInstance()->_definition[$model] = sfCouchdbJsonDefinitionParser::parse($model, $schema[$model]);
            return self::getInstance()->_definition[$model];
        } else {
            return self::getInstance()->_definition[$model];
        }
    }

    public static function getDefinitionByHash($model, $hash) {
        if (!isset(self::getInstance()->_definition_hash[$model][$hash])) {
            self::getInstance()->_definition_hash[$model][$hash] = self::getDefinition($model)->getDefinitionByHash($hash);
            return self::getInstance()->_definition_hash[$model][$hash];
        } else {
            return self::getInstance()->_definition_hash[$model][$hash];
        }

        return ;
    }

    public static function getDefinitionHashTree($model, $class_tree) {
        if (!isset(self::getInstance()->_definition_tree_hash[$class_tree])) {
            self::getInstance()->_definition_tree_hash[$class_tree] = self::getDefinition($model)->findHashByClassName($class_tree);
            return self::getInstance()->_definition_tree_hash[$class_tree];
        } else {
            return self::getInstance()->_definition_tree_hash[$class_tree];
        }
    }
}
