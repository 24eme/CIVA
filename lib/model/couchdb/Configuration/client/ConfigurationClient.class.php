<?php

class ConfigurationClient extends sfCouchdbClient {
  private static $configuration = array();
  private static $current = null;

  public static function getConfiguration($year = '') {
    if (!$year) {
      if (!self::$current)
        self::$current = CurrentClient::getCurrent();
      $year = self::$current->year;
    }
    if (!isset(self::$configuration[$year])) {
        self::$configuration[$year] = CacheFunction::cache('model', array(sfCouchdbManager::getClient(), 'retrieveDocumentById'), array('CONFIGURATION-'.$year));
    }
    return self::$configuration[$year];
  }
  public function retrieveConfiguration($year = '') {
    if (!$year) {
      $y = parent::retrieveDocumentById('CURRENT');
    }
    return parent::retrieveDocumentById('CONFIGURATION-'.$year);
  }
}
