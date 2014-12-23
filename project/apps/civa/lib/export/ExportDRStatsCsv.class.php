<?php

class ExportDRStatsCsv {

    protected $ids = array();

    public function __construct($ids, $campagne) {

        $this->ids = $ids;
        $this->campagne = $campagne;
        $this->config = acCouchdbManager::getClient('Configuration')->retrieveConfiguration($this->campagne);
    }

    public function export() {
        $stats['superficie'] = 0;
        $stats['volume'] = 0;
        $stats['appellations'] = array();
        $n=0;

        foreach ($this->ids as $id) {
            $dr = acCouchdbManager::getClient("DR")->find($id, acCouchdbClient::HYDRATE_JSON);

            if(!isset($dr)) {

                continue;
            }

            if($dr->campagne != $this->campagne) {

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
                        $stats['appellations'][$appellation_key]['cepages'] = array();
                    }

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
                $stats['volume'] += $appellation->total_volume;
            }
        }

        echo "Appellation;Cépage;Superficie (ares);Volume (hl) bruts déclarés en production (dépassements de remdement inclus) \n";

        foreach($this->config->recolte->getNoeudAppellations() as $appellation_key => $config_appellation) {
                if(!isset($stats['appellations'][$appellation_key])) {
                    continue;

                }
                $appellation = $stats['appellations'][$appellation_key];

                foreach($config_appellation->getProduits() as $config_cepage) { 
                    if(!isset($stats['appellations'][$appellation_key]['cepages'][$config_cepage->getKey()])) {
                        continue;

                    }

                    if($config_cepage->excludeTotal()) {
                        continue;
                    }

                    $cepage = $appellation['cepages'][$config_cepage->getKey()];
                    echo sprintf("%s;%s;%01.02f;%01.02f\n", $config_appellation->getLibelle(), $config_cepage->getLibelle(), $cepage['superficie'],$cepage['volume']);
                    unset($stats['appellations'][$appellation_key]['cepages'][$config_cepage->getKey()]);
                }

                echo sprintf("%s;Total;%01.02f;%01.02f\n", $config_appellation->getLibelle(), $appellation['superficie'],$appellation['volume']);
                unset($stats['appellations'][$appellation_key]);
        }

        echo sprintf("Total général;;%01.02f;%01.02f\n", $stats['superficie'],$stats['volume']);

        

        echo sprintf("Nombre de déclarants;%s",$n)."\n";
    }
}