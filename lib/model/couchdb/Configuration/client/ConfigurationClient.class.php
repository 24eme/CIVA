<?php

class ConfigurationClient extends sfCouchdbClient {
  private static $configuration = null;
  public static function getConfiguration() {
    if (self::$configuration == null) {
      $function_cache = new sfFunctionCache(new sfFileCache(array('cache_dir' => sfConfig::get('sf_app_cache_dir').'/'.'couchdb')));
      self::$configuration = $function_cache->call(array(sfCouchdbManager::getClient(), 'retrieveDocumentById'), array('CONFIGURATION'));
      //self::$configuration = sfCouchdbManager::getClient()->retrieveDocumentById('CONFIGURATION');
    }
    return self::$configuration;
  }
  public function retrieveConfiguration() {
    return parent::retrieveDocumentById('CONFIGURATION');
  }
}
