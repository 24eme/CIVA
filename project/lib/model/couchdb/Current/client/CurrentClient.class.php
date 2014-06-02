<?php

class CurrentClient extends acCouchdbClient {
  private static $current = array();

  public static function getCurrent() {
    if(acCouchdbManager::getClient("Current")->hasCurrentFromTheFuture()) {

      return acCouchdbManager::getClient("Current")->getCurrentFromTheFuture();
    }
    
    if (self::$current == null) {
        self::$current = CacheFunction::cache('model', array(acCouchdbManager::getClient("Current"), 'retrieveCurrent'), array());
    }
    return self::$current;
  }

  public function retrieveCurrent() {
    return parent::find('CURRENT');
  }

  public function hasCurrentFromTheFuture() {
    if(sfContext::getInstance()->getUser()->getAttribute('back_to_the_future', null)) {
      return true;
    }

    return false;
  }

  public function getCurrentFromTheFuture() {
    if(!$this->hasCurrentFromTheFuture()) {

      return false;
    }

    $campagne = sfContext::getInstance()->getUser()->getAttribute('back_to_the_future', null);

    $current = $this->retrieveCurrent();

    $current->campagne = $campagne;
    $current->dr_non_editable = 1;
    $current->ds_non_editable = 1;

    return $current;
  }
}
