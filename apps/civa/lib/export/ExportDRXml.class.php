<?php

class ExportDRXml {
    protected $content = null;
    protected $partial_function = null;


    public static function sortXML($a, $b) {
        $a = preg_replace('/L/', '', $a);
        $b = preg_replace('/L/', '', $b);

        $a = preg_replace('/_[0-9]+/', '', $a);
        $b = preg_replace('/_[0-9]+/', '', $b);
        
        return $a > $b;
    }

    private static $type2douane = array('negoces' => 'L6', 'mouts' => 'L7', 'cooperatives' => 'L8');

    private function setAcheteursForXml(&$xml, $obj, $type) {
        $acheteurs = array();
        foreach($obj->getVolumeAcheteurs($type) as $cvi => $volume) {
            $item = array('numCvi' => $cvi, 'volume' => $volume);
            $xml[self::$type2douane[$type].'_'.$cvi] = $item;
        }
    }

    public function  __construct($dr, $partial_function) {
        $this->partial_function = $partial_function;
        $this->create($dr);
    }
    
    public function getContent() {
        return $this->content;
    }

    protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
    }

    protected function create($dr) {
        $xml = array();
        foreach ($dr->recolte->getConfigAppellations() as $appellation_config) {
            if (!$dr->recolte->exist($appellation_config->getKey())) {
                continue;
            }
            $appellation = $dr->recolte->get($appellation_config->getKey());
            foreach ($appellation->getConfig()->getLieux() as $lieu_config) {
                if (!$appellation->exist($lieu_config->getKey())) {
                    continue;
                }
                $lieu = $appellation->get($lieu_config->getKey());
                //Comme il y a plusieurs acheteurs par lignes, il faut passer par une structure intermédiaire
                $acheteurs = array();
                $total = array();

                $total['L1'] = $lieu->getCodeDouane();
                $total['L3'] = 'B';
                $total['L4'] = $lieu->getTotalSuperficie();
                $total['exploitant'] = array();
                $total['exploitant']['L5'] = $lieu->getTotalVolume();
                $this->setAcheteursForXml($total['exploitant'], $lieu, 'negoces');
                $this->setAcheteursForXml($total['exploitant'], $lieu, 'mouts');
                $this->setAcheteursForXml($total['exploitant'], $lieu, 'cooperatives');
                $total['exploitant']['L9'] = $lieu->getTotalCaveParticuliere();
                $total['exploitant']['L10'] = $lieu->getTotalCaveParticuliere() + $lieu->getTotalVolumeAcheteurs('cooperatives'); //Volume revendique non negoces
                $total['exploitant']['L11'] = 0; //HS
                $total['exploitant']['L12'] = 0; //HS
                $total['exploitant']['L13'] = 0; //HS
                $total['exploitant']['L14'] = 0; //Vin de table + Rebeches
                $l15 = $lieu->volume_revendique - $lieu->getTotalVolumeAcheteurs('negoces') - $lieu->getTotalVolumeAcheteurs('mouts');
                if ($l15 < 0) {
                    $l15 = 0;
                }
                $total['exploitant']['L15'] = $l15; //Volume revendique
                $total['exploitant']['L16'] = $lieu->dplc; //DPLC
                $total['exploitant']['L17'] = 0; //HS
                $total['exploitant']['L18'] = 0; //HS
                $total['exploitant']['L19'] = 0; //HS
                $colass = null;
                foreach ($lieu->getConfig()->getCepages() as $cepage_config) {
                    if (!$lieu->exist($cepage_config->getKey())) {
                        continue;
                    }
                    $cepage = $lieu->get($cepage_config->getKey());
                    foreach ($cepage->detail as $detail) {

                        $col = array();

                        $col['L1'] = $detail->getCodeDouane();

                        // SI PAS D'AUTRE AOC
                        if ($appellation->getKey() == 'appellation_VINTABLE' && $dr->recolte->getAppellations()->count() > 1) {
                            $col['L1'] = $detail->getCepage()->getCodeDouane('AOC');
                        }

                        $col['L3'] = 'B';
                        $col['mentionVal'] = $detail->denomination;
                        $col['L4'] = $detail->superficie;
                        
                        $col['exploitant'] = array();
                        $col['exploitant']['L5'] = $detail->volume ; //Volume total sans lies

                        $this->setAcheteursForXml($col['exploitant'], $detail, 'negoces');
                        $this->setAcheteursForXml($col['exploitant'], $detail, 'mouts');
                        $this->setAcheteursForXml($col['exploitant'], $detail, 'cooperatives');

                        $col['exploitant']['L9'] = $detail->cave_particuliere; //Volume revendique sur place
                        $col['exploitant']['L10'] = $detail->cave_particuliere + $detail->getTotalVolumeAcheteurs('cooperatives'); //Volume revendique non negoces
                        $col['exploitant']['L11'] = 0; //HS
                        $col['exploitant']['L12'] = 0; //HS
                        $col['exploitant']['L13'] = 0; //HS
                        $col['exploitant']['L14'] = 0; //Vin de table + Rebeches
                        $col['exploitant']['L15'] = 0; //Volume revendique
                        $col['exploitant']['L16'] = 0; //DPLC
                        $col['exploitant']['L17'] = 0; //HS
                        $col['exploitant']['L18'] = 0; //HS
                        $col['exploitant']['L19'] = 0; //HS

                        if (($cepage->getKey() == 'cepage_RB' && $appellation->getKey() == 'appellation_CREMANT') || $appellation->getKey() == 'appellation_VINTABLE') {
                            $col['exploitant']['L14'] = $detail->volume;
                        } else {
                            $col['exploitant']['L15'] = $detail->volume;
                        }


                        uksort($col['exploitant'], 'exportDRXml::sortXML');

                        if ($detail->exist('motif_non_recolte') && $detail->motif_non_recolte) {
                            $col['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                        } elseif(!$detail->volume || $detail->volume == 0) {
                            if ($appellation->getKey() == 'appellation_ALSACEBLANC' &&
                                $dr->recolte->exist('appellation_ALSACEBLANC') &&
                                $dr->recolte->get('appellation_ALSACEBLANC')->lieu->exist('cepage_ED') &&
                                $dr->recolte->get('appellation_ALSACEBLANC')->lieu->get('cepage_ED')->getTotalVolume() > 0) {
                                $col['motifSurfZero'] = 'AE';
                            } else {
                                $col['motifSurfZero'] = 'PC';
                            }
                        }

                        if ($cepage->getKey() == 'cepage_RB' && $appellation->getKey() == 'appellation_CREMANT') {
                            unset($col['L3'], $col['L4'], $col['mentionVal']);
                            $colass = $col;
                        } elseif($lieu->getAppellation()->getAppellation() != 'KLEVENER') {
                            $xml[] = $col;
                        } elseif($lieu->getAppellation()->getAppellation() == 'KLEVENER') {
                            if (!($lieu->getTotalVolume() > 0)) {
                                if ($detail->exist('motif_non_recolte') && $detail->motif_non_recolte) {
                                    $total['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                                } elseif(!isset($total['motifSurfZero'])) {
                                    $total['motifSurfZero'] = 'PC';
                                }
                            }
                        }
                    }
                }

                if ($lieu->getTotalCaveParticuliere()) {

                    $total['exploitant']['L5'] += $lieu->getTotalCaveParticuliere() * $dr->getRatioLies();  //Volume total avec lies
                    $total['exploitant']['L9'] += $lieu->getTotalCaveParticuliere() * $dr->getRatioLies();
                    $total['exploitant']['L10'] += $lieu->getTotalCaveParticuliere() * $dr->getRatioLies();
                }
                uksort($total['exploitant'], 'exportDRXml::sortXML');

                if ($colass) {
                    $total['colonneAss'] = $colass;
                }
                if ($lieu->getAppellation()->getAppellation() != 'VINTABLE') {
                    $xml[] = $total;
                }
            }
        }

        $this->content = $this->getPartial('export/xml', array('dr' => $dr, 'xml' => $xml));
    }
}
?>
