<?php

$_APPELLATION = "AOC Alsace blanc";
$_CEPAGE      = "Pinot Gris";
$_RENDEMENT_LIMITE = 56.0;
$_RENDEMENT_MAX    = 70.0;

if (count($argv) !== 2) {
    die('Il manque des arguments. Usage : php ' . $argv[0] . ' export-sv.csv'.PHP_EOL);
}

$export_sv = fopen($argv[1], 'r');
$output = fopen('php://output', 'w+');

if ($export_sv === false || $output === false) {
    die("Erreur à l'ouverture des fichiers");
}

$current_cvi = null;
$current_rs = null;
$current_type = null;
$current_superficie = 0;
$current_volume = 0;

fputcsv($output, [
    'Type', 'CVI', 'Raison sociale', 'Superficie', 'Volume revendiqué', 'Rendement', 'Réserve calculée', 'Réserve notifiée'
], ';');

while (($ligne = fgetcsv($export_sv, 1000, ";")) !== false) {
    if (strpos($ligne[4], $_APPELLATION) !== 0) {
        continue;
    }

    if ($ligne[6] !== $_CEPAGE) {
        continue;
    }

    if ($ligne[7] === 'VT' || $ligne[7] === 'SGN') {
        continue;
    }

    if ($current_cvi !== null && $current_cvi !== $ligne[0]) {
        $rendement = round($current_volume / ($current_superficie / 100), 2);

        $reserve_calculee = 0;
        $reserve_notifiee = 0;

        if ($rendement > $_RENDEMENT_LIMITE) {
            $rendement_calcul = $rendement;

            if ($rendement > $_RENDEMENT_MAX) {
                $rendement_calcul = $_RENDEMENT_MAX;
            }

            $reserve_calculee = $reserve_notifiee = round(($rendement_calcul - $_RENDEMENT_LIMITE) * ($current_superficie / 100), 2);
        }

        if ($reserve_notifiee <= 5.0) {
            $reserve_notifiee = 0;
        }

        fputcsv($output, [
            $current_type, // SV11 / SV12
            $current_cvi, // CVI
            $current_rs, // Raison sociale
            $current_superficie,
            $current_volume,
            $rendement,
            $reserve_calculee,
            $reserve_notifiee
        ], ';');

        $current_superficie = 0;
        $current_volume = 0;
    }

    $current_superficie += $ligne[9];
    $current_volume     += $ligne[14];
    $current_cvi = $ligne[0];
    $current_rs = $ligne[1];
    $current_type = $ligne[15];
}

fclose($export_sv);
fclose($output);
