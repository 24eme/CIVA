<?php

class ListAcheteursConfig {

    protected static $_negoces = null;
    protected static $_negoces_json = null;
    protected static $_cooperatives = null;
    protected static $_cooperatives_json = null;
    protected static $_mouts = null;
    protected static $_mouts_json = null;

    protected static function getJson($items) {
        $newitems = array();
        foreach ($items as $cvi => $item) {
            $newitems[] = $item['nom'] . '|@' . $item['cvi'] . '|@' . $item['commune'];
        }

        return json_encode($newitems);
    }

    protected static function getOut($items, $cvis) {
        $newitems = array();
        foreach($items as $cvi => $item) {
            if (!in_array($cvi, $cvis)) {
                $newitems[$cvi] = $item;
            }
        }
        return $newitems;
    }

    protected static function getIn($items, $cvis) {
        $newitems = array();
        foreach($items as $cvi => $item) {
            if (in_array($cvi, $cvis)) {
                $newitems[$cvi] = $item;
            }
        }
        return $newitems;
    }

    protected static function getInOut($items, $in = null, $out = null) {
        if ($in !== null) {
            return self::getIn($items, $in);
        } elseif($out !== null) {
            return self::getOut($items, $out);
        } else {
            return $items;
        }
    }


    public static function getNegoces() {
        if (is_null(self::$_negoces)) {
            self::$_negoces = include(sfConfig::get('sf_data_dir') . '/acheteurs-negociant.php');
        } 

        return self::$_negoces;
        
    }

    public static function getNegocesJson($in = null, $out = null) {
        return self::getJson(self::getInOut(self::getNegoces(), $in, $out));
    }

    public static function getCooperatives() {
        if (is_null(self::$_cooperatives)) {
            self::$_cooperatives = include(sfConfig::get('sf_data_dir') . '/acheteurs-cave.php');
        } 

        return self::$_cooperatives;
    }

    public static function getCooperativesJson($in = null, $out = null) {
         return self::getJson(self::getInOut(self::getCooperatives(), $in, $out));
    }

    public static function getMouts() {
        if (is_null(self::$_mouts)) {
            self::$_mouts = include(sfConfig::get('sf_data_dir') . '/acheteurs-mout.php');
        } 

        return self::$_mouts;
    }

    public static function getMoutsJson($in = null, $out = null) {
        return self::getJson(self::getInOut(self::getMouts(), $in, $out));
    }
}