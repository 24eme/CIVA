<?php

class ListAcheteursConfig {

    protected static $_negoces = null;
    protected static $_cooperatives = null;
    protected static $_mouts = null;

    public static function getNegoces() {
        if (is_null(self::$_negoces)) {
            self::$_negoces = include(sfConfig::get('sf_data_dir') . '/acheteurs-negociant.php');
        } 

        return self::$_negoces;
        
    }
    public static function getCooperatives() {
        if (is_null(self::$_cooperatives)) {
            self::$_cooperatives = include(sfConfig::get('sf_data_dir') . '/acheteurs-cave.php');
        } 

        return self::$_cooperatives;
    }
    public static function getMouts() {
        if (is_null(self::$_mouts)) {
            self::$_mouts = include(sfConfig::get('sf_data_dir') . '/acheteurs-mout.php');
        } 

        return self::$_mouts;
    }
}