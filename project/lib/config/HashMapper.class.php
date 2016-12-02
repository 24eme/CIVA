<?php

class HashMapper {

    public static $convert_hash = array();
    public static $inverse_hash = array();

    public static function convert($hash) {
        $hashOrigine = $hash;
        if(array_key_exists($hash, self::$convert_hash)) {

            return self::$convert_hash[$hash];
        }

        $hash = preg_replace("|^/recolte|", "/declaration", $hash);
        $hash = preg_replace("|/certification|", "/certifications/AOC_ALSACE", $hash);
        $hash = preg_replace("|/genre|", "/genres/TRANQ", $hash);
        $hash = preg_replace("|/appellation_([a-zA-Z0-9_-]+)|", "/appellations/$1", $hash);
        $hash = preg_replace("|/mention/|", "/mentions/DEFAUT/", $hash);
        $hash = preg_replace("|/mention$|", "/mentions/DEFAUT", $hash);
        $hash = preg_replace("|/mention([A-Z0-9]+)/|", "/mentions/$1/", $hash);
        $hash = preg_replace("|/mention([A-Z0-9]+)$|", "/mentions/$1", $hash);
        $hash = preg_replace("|/lieu/|", "/lieux/DEFAUT/", $hash);
        $hash = preg_replace("|/lieu$|", "/lieux/DEFAUT", $hash);
        $hash = preg_replace("|/lieu([A-Z0-9]+)/|", "/lieux/$1/", $hash);
        $hash = preg_replace("|/lieu([A-Z0-9]+)$|", "/lieux/$1", $hash);
        $hash = preg_replace("|/couleur/|", "/couleurs/DEFAUT/", $hash);
        $hash = preg_replace("|/couleur$|", "/couleurs/DEFAUT", $hash);
        $hash = preg_replace("|/couleur([a-zA-Z0-9_-]{2,30})/|", "/couleurs/$1/", $hash);
        $hash = preg_replace("|/couleur([a-zA-Z0-9_-]{2,30})$|", "/couleurs/$1", $hash);
        $hash = preg_replace("|/cepage_([a-zA-Z0-9_-]+)|", "/cepages/$1", $hash);
        $hash = str_replace("couleur/Rouge", "couleur/rouge", $hash);
        $hash = str_replace("couleur/Blanc", "couleur/blanc", $hash);
        $hash = preg_replace("|/genres/TRANQ/appellations/CREMANT|", "/genres/EFF/appellations/CREMANT", $hash);
        $hash = preg_replace("|/certifications/AOC_ALSACE/genres/TRANQ/appellations/VINTABLE|", "/certifications/VINSSIG/genres/TRANQ/appellations/VINTABLE", $hash);

        self::$convert_hash[$hashOrigine] = $hash;

        return $hash;
    }

    public static function inverse($hash) {
        $hashOrigine = $hash;
        if(array_key_exists($hash, self::$inverse_hash)) {

            return self::$inverse_hash[$hash];
        }

        $hash = preg_replace("|^/declaration|", "/recolte", $hash);
        $hash = preg_replace("|/certifications/AOC_ALSACE|", "/certification", $hash);
        $hash = preg_replace("|/certifications/VINSSIG|", "/certification", $hash);
        $hash = preg_replace("|/genres/TRANQ|", "/genre", $hash);
        $hash = preg_replace("|/appellations/([a-zA-Z0-9_-]+)|", "/appellation_$1" , $hash);
        $hash = preg_replace("|/mentions/DEFAUT/|", "/mention/", $hash);
        $hash = preg_replace("|/mentions/DEFAUT|", "/mention", $hash);
        $hash = preg_replace("|/mentions/([A-Z0-9]+)/|", "/mention$1/", $hash);
        $hash = preg_replace("|/mentions/([A-Z0-9]+)$|", "/mention$1", $hash);
        $hash = preg_replace("|/lieux/DEFAUT/|", "/lieu/", $hash);
        $hash = preg_replace("|/lieux/DEFAUT|", "/lieu", $hash);
        $hash = preg_replace("|/lieux/([A-Z0-9]+)/|", "/lieu$1/", $hash);
        $hash = preg_replace("|/lieux/([A-Z0-9]+)$|", "/lieu$1", $hash);
        $hash = preg_replace("|/couleurs/DEFAUT/|", "/couleur/", $hash);
        $hash = preg_replace("|/couleurs/DEFAUT$|", "/couleur",$hash);
        $hash = preg_replace("|/couleurs/([a-zA-Z0-9_-]{2,30})/|", "/couleur$1/", $hash);
        $hash = preg_replace("|/couleurs/([a-zA-Z0-9_-]{2,30})$|", "/couleur$1", $hash);
        $hash = str_replace("couleurblanc", "couleurBlanc", $hash);
        $hash = str_replace("couleurrouge", "couleurRouge", $hash);
        $hash = preg_replace("|/cepages/([a-zA-Z0-9_-]+)|", "/cepage_$1", $hash);
        $hash = preg_replace("|/genres/EFF|", "/genre", $hash);

        self::$inverse_hash[$hashOrigine] = $hash;

        return $hash;
    }

}
