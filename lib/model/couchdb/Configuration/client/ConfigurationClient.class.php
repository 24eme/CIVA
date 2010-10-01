<?php

class ConfigurationClient extends sfCouchdbClient {
  private static $configuration = null;
  public static function getConfiguration() {
    if (self::$configuration == null) {
        self::$configuration = CacheFunction::cache('model', array(sfCouchdbManager::getClient(), 'retrieveDocumentById'), array('CONFIGURATION'));
    }
    return self::$configuration;
  }
  public function retrieveConfiguration() {
    return parent::retrieveDocumentById('CONFIGURATION');
  }
}
