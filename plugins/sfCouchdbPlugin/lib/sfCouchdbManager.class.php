<?php

class sfCouchdbManager {
    protected static $_instance;

    protected $_client;
    
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
    }

    public static function getClient() {
        return self::getInstance()->_client;
    }
}
