<?php
class CacheFunction {
    const CLASS_CACHE = "sfFileCache";
    const AUTOMATIC_CLEANING_FACTOR = 0;
    const LIFETIME = 31556926;

    public static function cache($location, $callable, $arguments = array()) {
      $class_cache = self::CLASS_CACHE;
      $cache_dir = sfConfig::get('sf_app_cache_dir').DIRECTORY_SEPARATOR.$location;
      $function_cache = new sfFunctionCache(new $class_cache(array('cache_dir' => $cache_dir)));
      return $function_cache->call($callable, $arguments);
    }
}
