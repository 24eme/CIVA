<?php

$_APPELLATION = "AOC Alsace blanc";
$_CEPAGE      = "TOTAL Pinot Gris";
$_RENDEMENT_LIMITE = 56.0;
$_RENDEMENT_MAX    = 70.0;

if (count($argv) !== 2) {
    die('Il manque des arguments. Usage : php ' . $argv[0] . ' export-dr.csv'.PHP_EOL);
}

$export_dr = fopen($argv[1], 'r');
$output = fopen('php://output', 'w+');
$max_rendement = [];

if ($export_dr === false || $output === false) {
    die("Erreur à l'ouverture des fichiers");
}

fputcsv($output, [
    'Type', 'CVI', 'Raison sociale', 'Superficie totale', 'Superficie sur place', 'Volume revendiqué total', 'Rendement', 'Réserve calculée', 'Réserve notifiée'
], ';');

while (($ligne = fgetcsv($export_dr, 1000, ";")) !== false) {
    if ($ligne[1] !== "SUR PLACE") {
        continue;
    }

    if ($ligne[4] !== $_APPELLATION) {
        continue;
    }

    if ($ligne[6] !== $_CEPAGE) {
        continue;
    }

    $surface_sur_place = $ligne[9];
    $surface_total = $ligne[12];
    $volume_total  = (float) $ligne[13] - (float) $ligne[14] - (float) $ligne[16]; // Volume - à détruire - vci

    if (is_null($surface_sur_place) || $surface_sur_place <= 0 || is_null($volume_total) || $volume_total <= 0) {
        continue;
    }

    $rendement = round($volume_total / ($surface_total / 100), 2);

    $reserve_calculee = 0;
    $reserve_notifiee = 0;

    if ($rendement > $_RENDEMENT_LIMITE) {
        $rendement_calcul = $rendement;

        if ($rendement > $_RENDEMENT_MAX) {
            $rendement_calcul = $_RENDEMENT_MAX;
        }

        $reserve_calculee = $reserve_notifiee = round(($rendement_calcul - $_RENDEMENT_LIMITE) * ($surface_sur_place / 100), 2);
    }

    if ($reserve_notifiee <= 5.0) {
        $reserve_notifiee = 0;
    }

    fputcsv($output, [
        'DR',
        $ligne[2], // CVI
        $ligne[3], // Raison sociale
        $surface_total,
        $surface_sur_place,
        $volume_total,
        $rendement,
        $reserve_calculee,
        $reserve_notifiee
    ], ';');
}

fclose($export_dr);
fclose($output);
