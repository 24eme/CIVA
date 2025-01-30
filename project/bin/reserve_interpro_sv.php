<?php

$_RENDEMENTS = [
    'Pinot Gris' => [
        'limite' => 52.0,
        'max'    => 65.0
    ],
    'Gewurztraminer' => [
        'limite' => 40.0,
        'max'    => 50.0
    ],
    'Riesling' => [
        'limite' => 60.0,
        'max'    => 75.0
    ]
];

if (count($argv) !== 3) {
    die('Il manque des arguments. Usage : php ' . $argv[0] . ' export-sv.csv \''.implode("'|'", array_keys($_RENDEMENTS)).'\''.PHP_EOL);
}

if (array_key_exists($argv[2], $_RENDEMENTS) === false) {
    die('Mauvais cépage renseigné. Usage : php ' . $argv[0] . ' export-sv.csv \''.implode("'|'", array_keys($_RENDEMENTS)).'\''.PHP_EOL);
}

$_APPELLATION = "AOC Alsace blanc";
$_CEPAGE = $argv[2];
$_RENDEMENT_LIMITE = $_RENDEMENTS[$argv[2]]['limite'];
$_RENDEMENT_MAX    = $_RENDEMENTS[$argv[2]]['max'];

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
    if (strpos($ligne[6], $_APPELLATION) !== 0) {
        continue;
    }

    if ($ligne[8] !== $_CEPAGE) {
        continue;
    }

    if ($ligne[9] === 'VT' || $ligne[9] === 'SGN') {
        continue;
    }

    if ($current_cvi !== null && $current_cvi !== $ligne[2]) {
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

    $current_superficie += $ligne[11];
    $current_volume     += $ligne[16];
    $current_cvi = $ligne[2];
    $current_rs = $ligne[3];
    $current_type = $ligne[0];
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

fclose($export_sv);
fclose($output);
