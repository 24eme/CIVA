<?php

class ConfigurationClient extends sfCouchdbClient {
  private static $configuration = null;
  public static function getConfiguration() {
    if (self::$configuration == null)
      self::$configuration = sfCouchdbManager::getClient()->retrieveDocumentById('CONFIGURATION');
    return self::$configuration;
  }
  public function retrieveConfiguration() {
    return parent::retrieveDocumentById('CONFIGURATION');
  }
}
