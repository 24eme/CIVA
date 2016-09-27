<?php

class ExportDSStatsCsv {

    protected $ids = array();

    public function __construct($ids, $campagne) {

        $this->ids = $ids;
        $this->campagne = $campagne;
        $this->config = ConfigurationClient::getConfiguration();
    }

    public function export() {
        $stats = array();
        $stats['volume_total'] = 0;
        $stats['volume_normal'] = 0;
        $stats['volume_vt'] = 0;
        $stats['volume_sgn'] = 0;
        $stats['appellations'] = array();
        $identifiants = array();

        foreach ($this->ids as $id) {
            $ds = DSCivaClient::getInstance()->find($id);
            $identifiants[$ds->identifiant] = true;

            $stats['volume_total'] += $ds->declaration->getTotalStock();
            $stats['volume_normal'] += $ds->declaration->getTotalNormal();
            $stats['volume_vt'] += $ds->declaration->getTotalVT();
            $stats['volume_sgn'] += $ds->declaration->getTotalSGN();
            foreach($ds->declaration->getAppellationsSorted() as $appellation) {
                if(!array_key_exists($appellation->getKey(), $stats['appellations'])) {
                    $stats['appellations'][$appellation->getKey()]['volume_total'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_normal'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_vt'] = 0;
                    $stats['appellations'][$appellation->getKey()]['volume_sgn'] = 0;
                    $stats['appellations'][$appellation->getKey()]['cepages'] = array();
                }

                $stats['appellations'][$appellation->getKey()]['volume_total'] += $appellation->getTotalStock();
                $stats['appellations'][$appellation->getKey()]['volume_normal'] += $appellation->getTotalNormal();
                $stats['appellations'][$appellation->getKey()]['volume_vt'] += $appellation->getTotalVT();
                $stats['appellations'][$appellation->getKey()]['volume_sgn'] += $appellation->getTotalSGN();

                foreach($appellation->getProduitsSorted() as $cepage) {
                    if(!array_key_exists($cepage->getKey(), $stats['appellations'][$appellation->getKey()]['cepages'])) {
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_total'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_normal'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_vt'] = 0;
                        $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_sgn'] = 0;
                    }

                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_total'] += $cepage->getTotalStock();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_normal'] += $cepage->getTotalNormal();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_vt'] += $cepage->getTotalVT();
                    $stats['appellations'][$appellation->getKey()]['cepages'][$cepage->getKey()]['volume_sgn'] += $cepage->getTotalSGN();
                }
            }
        }

        $content = "";
        $content .= "Appellation;Cépage;Volume total (hl);Volume normal (hl);Volume VT (hl);Volume SGN (hl)\n";

        $ligne = "%s;%s;%s;%s;%s;%s\n";
        $configuration = $this->config;

        foreach($configuration->declaration->getArrayAppellations() as $c_appellation) {
            $appellation_key = "appellation_".$c_appellation->getKey();
            if(!array_key_exists($appellation_key, $stats['appellations'])) {
                continue;
            }

            $appellation = $stats['appellations'][$appellation_key];

            foreach($c_appellation->getProduits() as $c_cepage) {
                $cepage_key = "cepage_".$c_cepage->getKey();
                if(!array_key_exists($cepage_key, $appellation['cepages'])) {
                    continue;
                }

                $cepage = $appellation['cepages'][$cepage_key];

                $content .= sprintf($ligne, $c_appellation->getLibelle(),
                                     $c_cepage->getLibelle(),
                                     $this->convertFloat2Fr($cepage['volume_total']),
                                     $this->convertFloat2Fr($cepage['volume_normal']),
                                     $this->convertFloat2Fr($cepage['volume_vt']),
                                     $this->convertFloat2Fr($cepage['volume_sgn']));

                unset($appellation['cepages'][$cepage_key]);
            }

            $content .= sprintf($ligne, $c_appellation->getLibelle(),
                                 "TOTAL",
                                 $this->convertFloat2Fr($appellation['volume_total']),
                                 $this->convertFloat2Fr($appellation['volume_normal']),
                                 $this->convertFloat2Fr($appellation['volume_vt']),
                                 $this->convertFloat2Fr($appellation['volume_sgn']));

            unset($stats['appellations'][$appellation_key]);
        }

        $content .= sprintf($ligne, "TOTAL Général",
                             "",
                             $this->convertFloat2Fr($stats['volume_total']),
                             $this->convertFloat2Fr($stats['volume_normal']),
                             $this->convertFloat2Fr($stats['volume_vt']),
                             $this->convertFloat2Fr($stats['volume_sgn']));

        $content .= sprintf("Nombre de déclarants;%s\n", count($identifiants));

        return $content;
    }

    protected function convertFloat2Fr($value) {

        return str_replace(".", ",", sprintf("%01.02f", $value));
    }
}
