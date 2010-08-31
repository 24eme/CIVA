<?php

class ListAcheteursConfig {

    protected static $_negoces = null;
    protected static $_negoces_json = null;
    protected static $_cooperatives = null;
    protected static $_cooperatives_json = null;
    protected static $_mouts = null;
    protected static $_mouts_json = null;

    public static function getNegoces() {
        if (is_null(self::$_negoces)) {
            self::$_negoces = include(sfConfig::get('sf_data_dir') . '/acheteurs-negociant.php');
        } 

        return self::$_negoces;
        
    }

    public static function getNegocesJson() {
        if (is_null(self::$_negoces_json)) {
            self::$_negoces_json = array();
            foreach (self::getNegoces() as $cvi => $item) {
                self::$_negoces_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
            }
        }

        return json_encode(self::$_negoces_json);
    }

    public static function getCooperatives() {
        if (is_null(self::$_cooperatives)) {
            self::$_cooperatives = include(sfConfig::get('sf_data_dir') . '/acheteurs-cave.php');
        } 

        return self::$_cooperatives;
    }

    public static function getCooperativesJson() {
        if (is_null(self::$_cooperatives_json)) {
            self::$_cooperatives_json = array();
            foreach (self::getCooperatives() as $cvi => $item) {
                self::$_cooperatives_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
            }
        }

        return json_encode(self::$_cooperatives_json);
    }

    public static function getMouts() {
        if (is_null(self::$_mouts)) {
            self::$_mouts = include(sfConfig::get('sf_data_dir') . '/acheteurs-mout.php');
        } 

        return self::$_mouts;
    }

    public static function getMoutsJson() {
        if (is_null(self::$_mouts_json)) {
            self::$_mouts_json = array();
            foreach (self::getMouts() as $cvi => $item) {
                self::$_mouts_json[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
            }
        }

        return json_encode(self::$_mouts_json);
    }
}