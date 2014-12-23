<?php

class ExportDRStatsCsv {

    protected $ids = array();

    public function __construct($ids) {

        $this->ids = $ids;
    }

    public function export() {
        $stats['superficie'] = 0;
        $stats['volume'] = 0;
        $stats['volume_revendique'] = 0;
        $stats['usages_industriels'] = 0;
        $stats['appellations'] = array();
        $n=0;

        foreach ($this->ids as $id) {
            $dr = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);

            if(!isset($dr)) {

                continue;
            }

            $n++;

            if(!isset($dr->recolte->certification->genre)) {

                continue;
            }

            foreach($dr->recolte->certification->genre as $appellation_key => $appellation) {
                if (!preg_match("/^appellation/", $appellation_key)) {

                    continue;
                }

                foreach($appellation->mention as $lieu_key => $lieu) {
                    if (!preg_match("/^lieu/", $lieu_key)) {

                        continue;
                    }

                    if(!array_key_exists($appellation_key, $stats['appellations'])) {
                        $stats['appellations'][$appellation_key]['superficie'] = 0;
                        $stats['appellations'][$appellation_key]['volume'] = 0;
                        $stats['appellations'][$appellation_key]['volume_revendique'] = 0;
                        $stats['appellations'][$appellation_key]['usages_industriels'] = 0;
                        $stats['appellations'][$appellation_key]['cepages'] = array();
                    }

                    $stats['appellations'][$appellation_key]['volume_revendique'] += $lieu->volume_revendique;
                    $stats['appellations'][$appellation_key]['usages_industriels'] += $lieu->usages_industriels;
                    $stats['volume_revendique'] += $lieu->volume_revendique;
                    $stats['usages_industriels'] += $lieu->usages_industriels;

                    foreach($lieu as $couleur_key => $couleur) {
                        if (!preg_match("/^couleur/", $couleur_key)) {

                            continue;
                        }

                        foreach($couleur as $cepage_key => $cepage) {
                            if (!preg_match("/^cepage/", $cepage_key)) {

                                continue;
                            }


                            if(!array_key_exists($cepage_key, $stats['appellations'][$appellation_key]['cepages'])) {
                                $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] = 0;
                                $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] = 0;
                            }

                            $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['superficie'] += $cepage->total_superficie;
                            $stats['appellations'][$appellation_key]['cepages'][$cepage_key]['volume'] += $cepage->total_volume;
                        }
                    }
                }

                $stats['appellations'][$appellation_key]['superficie'] += $appellation->total_superficie;
                $stats['appellations'][$appellation_key]['volume'] += $appellation->total_volume;
                $stats['superficie'] += $appellation->total_superficie;
            }
        }

        echo "appellation;cepage;superficie;volume;volume_revendique;usages_industriels\n";

        foreach($stats['appellations'] as $appellation_key => $appellation) {
            foreach($appellation['cepages'] as $cepage_key => $cepage) {
                echo sprintf("%s;%s;%01.02f;%01.02f;;\n", $appellation_key, $cepage_key, $cepage['superficie'],$cepage['volume']);
            }

            echo sprintf("%s;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f\n", $appellation_key, $appellation['superficie'],$appellation['volume'],$appellation['volume_revendique'],$appellation['usages_industriels']);
        }

        echo sprintf("TOTAL;TOTAL;%01.02f;%01.02f;%01.02f;%01.02f\n", $stats['superficie'],$stats['volume'],$stats['volume_revendique'],$stats['usages_industriels']);

        

        echo sprintf("NB_DR;%s",$n)."\n";
    }
}