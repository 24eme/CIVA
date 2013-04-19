<?php

class CurrentClient extends acCouchdbClient {
  private static $current = array();

  public static function getCurrent() {
    if (self::$current == null) {
        self::$current = CacheFunction::cache('model', array(acCouchdbManager::getClient("Current"), 'retrieveCurrent'), array());
    }
    return self::$current;
  }
  public function retrieveCurrent() {
    return parent::find('CURRENT');
  }
}
