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

    $surface = $ligne[9];
    $volume  = (float) $ligne[10] - (float) $ligne[11] - (float) $ligne[15]; // Volume - à détruire - vci

    if (is_null($surface) || $surface <= 0 || is_null($volume) || $volume <= 0) {
        continue;
    }

    $rendement = round($volume / ($surface / 100), 2);

    if ($rendement > 70) {
        $max_rendement[$ligne[0]] = [$surface, $rendement, (float) $ligne[10], (float) $ligne[11], (float) $ligne[15], $volume];
    }

    $reserve_calculee = 0;
    $reserve_notifiee = 0;

    if ($rendement > $_RENDEMENT_LIMITE) {
        $reserve_calculee = $reserve_notifiee = round($rendement - $_RENDEMENT_LIMITE, 2);
    }

    if ($reserve_notifiee <= 5.0) {
        $reserve_notifiee = 0;
    }

    fputcsv($output, [
        'DR',
        $ligne[0], // CVI
        $ligne[3], // Raison sociale
        $surface,
        $volume,
        $rendement,
        $reserve_calculee,
        $reserve_notifiee
    ], ';');
}

fclose($export_dr);
fclose($output);
