<?php

class Flag extends BaseFlag {

  public static function getInstance() {
    $flag = acCouchdbManager::getClient()->find('FLAG');
    if ($flag) {
      return $flag;
    }
    $flag = new Flag();
    $flag->_id = 'FLAG';
    $flag->type = 'Flag';
    return $flag;
  }

  public static function setFlag($key, $value) {
    $flag = Flag::getInstance();
    $flag->add($key, $value);
    $flag->save();
  }

  public static function getFlag($key, $default = null) {

    $flag = Flag::getInstance();

    if (!$flag->exist($key)) {

        return $default;
    }

    return $flag->get($key);
  }

}