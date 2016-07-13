<?php

class HashMapper {
    public static function convert($hash) {
        $hash = preg_replace("|^/recolte|", "/declaration", $hash);
        $hash = preg_replace("|/certification|", "/certifications/AOC_ALSACE", $hash);
        $hash = preg_replace("|/genre|", "/genres/TRANQ", $hash);
        $hash = preg_replace("|/appellation_([a-zA-Z0-9_-]+)|", "/appellations/$1", $hash);
        $hash = preg_replace("|/mention|", "/mentions/DEFAUT", $hash);
        $hash = preg_replace("|/lieu/|", "/lieux/DEFAUT/", $hash);
        $hash = preg_replace("|/lieu$|", "/lieux/DEFAUT", $hash);
        $hash = preg_replace("|/lieu([0-9]+)/|", "/lieux/$1/", $hash);
        $hash = preg_replace("|/lieu([0-9]+)$|", "/lieux/$1", $hash);
        $hash = preg_replace("|/couleur/|", "/couleurs/DEFAUT/", $hash);
        $hash = preg_replace("|/couleur$|", "/couleurs/DEFAUT", $hash);
        $hash = preg_replace("|/couleur([a-zA-Z0-9_-]{2,30})/|", "/couleurs/$1/", $hash);
        $hash = preg_replace("|/couleur([a-zA-Z0-9_-]{2,30})$|", "/couleurs/$1", $hash);
        $hash = preg_replace("|/cepage_([a-zA-Z0-9_-]+)|", "/cepages/$1", $hash);

        $hash = preg_replace("|/genres/TRANQ/appellations/CREMANT|", "/genres/EFF/appellations/CREMANT", $hash);
        $hash = preg_replace("|/certifications/AOC_ALSACE/genres/TRANQ/appellations/VINTABLE|", "/certifications/VINSSIG/genres/TRANQ/appellations/VINTABLE", $hash);

        return $hash;
    }

    public static function inverse($hash) {
        $hash = preg_replace("|^/declaration|", "/recolte", $hash);
        $hash = preg_replace("|/certifications/AOC_ALSACE|", "/certification", $hash);
        $hash = preg_replace("|/genres/TRANQ|", "/genre", $hash);
        $hash = preg_replace("|/appellations/([a-zA-Z0-9_-]+)|", "/appellation_$1" , $hash);
        $hash = preg_replace("|/mentions/DEFAUT|", "/mention" , $hash);
        $hash = preg_replace("|/lieux/DEFAUT/|", "/lieu/", $hash);
        $hash = preg_replace("|/lieux/DEFAUT|", "/lieu$", $hash);
        $hash = preg_replace("|/lieux/([0-9]+)/|", "/lieu$1", $hash);
        $hash = preg_replace("|/lieux/([0-9]+)$|", "/lieu$1", $hash);
        $hash = preg_replace("|/couleurs/DEFAUT/|", "/couleur/", $hash);
        $hash = preg_replace("|/couleurs/DEFAUT$|", "/couleur",$hash);
        $hash = preg_replace("|/couleurs/([a-zA-Z0-9_-]{2,30})/|", "/couleur$1/", $hash);
        $hash = preg_replace("|/couleurs/([a-zA-Z0-9_-]{2,30})$|", "/couleur$1", $hash);
        $hash = preg_replace("|/cepages/([a-zA-Z0-9_-]+)|", "/cepage_$1", $hash);

        $hash = preg_replace("|/genres/EFF/appellations/CREMANT|", "/genres/TRANQ/appellations/CREMANT", $hash);
        $hash = preg_replace( "|/certifications/VINSSIG/genres/TRANQ/appellations/VINTABLE|", "/certifications/AOC_ALSACE/genres/TRANQ/appellations/VINTABLE", $hash);

        return $hash;
    }

}
