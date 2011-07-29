<?php

class ConfigurationClient extends sfCouchdbClient {
  private static $configuration = array();
  private static $current = null;

  public static function getConfiguration($campagne = '') {
    if (!$campagne) {
      if (!self::$current)
        self::$current = CurrentClient::getCurrent();
      $campagne = self::$current->campagne;
    }
    if (!isset(self::$configuration[$campagne])) {
        self::$configuration[$campagne] = CacheFunction::cache('model', array(sfCouchdbManager::getClient(), 'retrieveDocumentById'), array('CONFIGURATION-'.$campagne));
    }
    return self::$configuration[$campagne];
  }
  public function retrieveConfiguration($campagne = '') {
    if (!$campagne) {
      $campagne = parent::retrieveDocumentById('CURRENT');
    }
    return parent::retrieveDocumentById('CONFIGURATION-'.$campagne);
  }
}
