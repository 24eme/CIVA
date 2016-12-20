<?php

class ExportDRXml {

    const DEST_DOUANE = 'Douane';
    const DEST_CIVA = 'Civa';

    protected $content = null;
    protected $partial_function = null;
    protected $destinataire = null;
    
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
            if($volume == 0) {
                continue;
            }
            $item = array('numCvi' => $cvi, 'volume' => $volume);
            $xml[self::$type2douane[$type].'_'.$cvi] = $item;
        }
    }

    public function  __construct($dr, $partial_function, $destinataire = self::DEST_DOUANE) {
        $this->partial_function = $partial_function;
        $this->destinataire = $destinataire;
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
<<<<<<< HEAD
        foreach ($dr->recolte->getNoeudAppellations()->getConfigAppellations() as $appellation_config) {
            if (!$dr->recolte->getNoeudAppellations()->exist($appellation_config->getKey())) {
=======
        $baliseachat = array();
        foreach ($dr->recolte->getConfig()->getArrayAppellations() as $appellationConfig) {
            if (!$dr->exist(HashMapper::inverse($appellationConfig->getHash()))) {
>>>>>>> d105e46... AJout des balises achat pour les achats faits auprès d'autres viti
                continue;
            }
            $appellation = $dr->recolte->getNoeudAppellations()->get($appellation_config->getKey());
            foreach ($appellation->getConfig()->getLieux() as $lieu_config) {
                if (!$appellation->getLieux()->exist($lieu_config->getKey())) {
                    continue;
                }
                $lieu = $appellation->getLieux()->get($lieu_config->getKey());
                //$usage_industriel_saisi = $lieu->getUsageIndustrielSaisi();

                foreach($lieu_config->getCouleurs() as $couleur_config) {
                    if (!$lieu->exist($couleur_config->getKey())) {
                        continue;
                    }
                    $couleur = $lieu->get($couleur_config->getKey());

                    $object = $lieu;
                    if ($lieu_config->hasManyCouleur()) {
                        $object = $couleur;
                    }

                    if($this->destinataire == self::DEST_DOUANE) {
                        if($appellation->getKey() == 'appellation_CREMANT') {
                            $col_total_cremant_blanc = null;
                            $col_total_cremant_rose = null;
                        }
                    }

<<<<<<< HEAD
                    $volume_revendique = $object->getVolumeRevendique();
                    $usages_industriels = $object->getUsagesIndustriels();

                    /*$volume_revendique = round($volume_revendique - $usage_industriel_saisi, 2);
                    $dplc = round($dplc + $usage_industriel_saisi, 2);*/

                    //$usage_industriel_saisi = 0;

                    //Comme il y a plusieurs acheteurs par lignes, il faut passer par une structure intermédiaire
                    $acheteurs = array();
                    $total = array();

                    $total['L1'] = $object->getCodeDouane();
                    $total['L3'] = 'B';
                    $total['L4'] = $object->getTotalSuperficie();
                    $total['exploitant'] = array();
                    $total['exploitant']['L5'] = $object->getTotalVolume();

                    $this->setAcheteursForXml($total['exploitant'], $object, 'negoces');
                    $this->setAcheteursForXml($total['exploitant'], $object, 'mouts');
                    $this->setAcheteursForXml($total['exploitant'], $object, 'cooperatives');
                    $total['exploitant']['L9'] = $object->getTotalCaveParticuliere();
                    $total['exploitant']['L10'] = $object->getTotalCaveParticuliere() + $object->getTotalVolumeAcheteurs('cooperatives'); //Volume revendique non negoces
                    $total['exploitant']['L11'] = 0; //HS
                    $total['exploitant']['L12'] = 0; //HS
                    $total['exploitant']['L13'] = 0; //HS
                    $total['exploitant']['L14'] = 0; //Vin de table + Rebeches
                    $l15 = $volume_revendique - $object->getTotalVolumeAcheteurs('negoces') - $object->getTotalVolumeAcheteurs('mouts');
                    if ($l15 < 0) {
                        $l15 = 0;
                    }
                    $total['exploitant']['L15'] = $l15; //Volume revendique
                    // Modifications suite au retour des douanes le total dplc total et celui du rendement appellation et plus de la somme pour les alsace blanc
                    $total['exploitant']['L16'] = $usages_industriels; //DPLC
                    $total['exploitant']['L17'] = 0; //HS
                    $total['exploitant']['L18'] = 0; //HS
                    $total['exploitant']['L19'] = 0; //HS

                    $colass = null;

                    if ($this->destinataire == self::DEST_DOUANE && 
                        count($couleur_config->getCepages()) == 1 && 
                        count($couleur->getCepages()) == 1 /*&&
                        !$couleur_config->getCepages()->getFirst()->hasVtsgn()*/) {
                        $cepage = $couleur->getCepages()->getFirst();
                        //$total['mentionVal'] = '';
                        foreach ($cepage->detail as $detail) {
                            if(count($cepage->detail) == 1) {
                                $detail = $cepage->detail[0];
                                if ($appellation_config->hasLieuEditable()) {
                                    //$total['mentionVal'] = $detail->lieu;    
                                } else {
                                    //$total['mentionVal'] = $detail->denomination;
                                }
                                if (!($object->getTotalVolume() > 0)) {
                                    if ($detail->exist('motif_non_recolte') &&  $detail->motif_non_recolte) {
                                        $total['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                                    } elseif(!isset($total['motifSurfZero'])) {
                                        $total['motifSurfZero'] = 'PC';
=======
                        $volume_revendique = $object->getVolumeRevendique();
                        $usages_industriels = $object->getUsagesIndustriels();

                        //Comme il y a plusieurs acheteurs par lignes, il faut passer par une structure intermédiaire
                        $acheteurs = array();
                        $total = array();

                        $total['L1'] = $this->getCodeDouane($object);
                        $total['L3'] = 'B';
                        $total['L4'] = $object->getTotalSuperficie();
                        $total['exploitant'] = array();
                        $total['exploitant']['L5'] = $object->getTotalVolume();

                        $this->setAcheteursForXml($total['exploitant'], $object, 'negoces');
                        $this->setAcheteursForXml($total['exploitant'], $object, 'mouts');
                        $this->setAcheteursForXml($total['exploitant'], $object, 'cooperatives');
                        $total['exploitant']['L9'] = $object->getTotalCaveParticuliere();
                        $total['exploitant']['L10'] = $object->getTotalCaveParticuliere() + $object->getTotalVolumeAcheteurs('cooperatives'); //Volume revendique non negoces
                        $total['exploitant']['L11'] = 0; //HS
                        $total['exploitant']['L12'] = 0; //HS
                        $total['exploitant']['L13'] = 0; //HS
                        $total['exploitant']['L14'] = 0; //Vin de table + Rebeches
                        $l15 = $volume_revendique - $object->getTotalVolumeAcheteurs('negoces') - $object->getTotalVolumeAcheteurs('mouts');
                        if ($l15 < 0) {
                            $l15 = 0;
                        }
                        $total['exploitant']['L15'] = $l15; //Volume revendique
                        // Modifications suite au retour des douanes le total dplc total et celui du rendement appellation et plus de la somme pour les alsace blanc
                        $total['exploitant']['L16'] = $usages_industriels; //DPLC
                        $total['exploitant']['L17'] = 0; //HS
                        $total['exploitant']['L18'] = 0; //HS
                        $total['exploitant']['L19'] = 0; //HS

                        if ($this->destinataire == self::DEST_DOUANE) {
                          foreach ($couleurConfig->getCepages() as $cepageConfig) {
                            if (!$dr->exist(HashMapper::inverse($cepageConfig->getHash()))) {
                                continue;
                            }
                            $cepage = $dr->get(HashMapper::inverse($cepageConfig->getHash()));
                            foreach ($cepage->detail as $detail) {
                              if (preg_match('/([0-9]{10})/', $detail->denomination, $m)) {
                                if (!isset($baliseachat[$m[0]])) {
                                  $baliseachat[$m[0]] = array('achat' => array('numCvi' => $m[0], 'motif' => 'SC', 'typeAchat' => 'F', 'volume' => 0));
                                }
                                $baliseachat[$m[0]]['achat']['volume'] += $detail->volume;
                              }
                            }
                          }
                        }

                        $colass = null;

                        if ($this->destinataire == self::DEST_DOUANE &&
                            count($couleurConfig->getCepages()) == 1 &&
                            count($couleur->getCepages()) == 1 /*&&
                        !$couleurConfig->getCepages()->getFirst()->hasVtsgn()*/) {
                            $cepage = $couleur->getCepages()->getFirst();
                            //$total['mentionVal'] = '';
                            foreach ($cepage->detail as $detail) {
                                if(count($cepage->detail) == 1) {
                                    $detail = $cepage->detail[0];
                                    if ($appellationConfig->hasLieuEditable()) {
                                        //$total['mentionVal'] = $detail->lieu;
                                    } else {
                                        //$total['mentionVal'] = $detail->denomination;
                                    }
                                    if (!($object->getTotalVolume() > 0)) {
                                        if ($detail->exist('motif_non_recolte') &&  $detail->motif_non_recolte) {
                                            $total['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                                        } elseif(!isset($total['motifSurfZero'])) {
                                            $total['motifSurfZero'] = 'PC';
                                        }
>>>>>>> d105e46... AJout des balises achat pour les achats faits auprès d'autres viti
                                    }
                                }
                            }
                        }
                    } else {
                        foreach ($couleur_config->getCepages() as $cepage_config) {
                            if (!$couleur->exist($cepage_config->getKey())) {
                                continue;
                            }
                            $cepage = $couleur->get($cepage_config->getKey());

                            if($this->destinataire == self::DEST_DOUANE) {
                                if (in_array($appellation->getKey(), array('appellation_ALSACEBLANC', 'appellation_LIEUDIT')) && $cepage->getKey() == 'cepage_ED') {
                                    continue;
                                }
                            }

                            $cols = array();
                            foreach ($cepage->detail as $detail) {

                                $col = array();

                                $col['L1'] = $detail->getCodeDouane();

                                // SI PAS D'AUTRE AOC
                                if ($appellation->getKey() == 'appellation_VINTABLE' && $dr->recolte->getNoeudAppellations()->getAppellations()->count() > 1) {
                                    $col['L1'] = $detail->getCepage()->getCodeDouane('AOC');
                                }

                                $col['L3'] = 'B';
                                if ($appellation_config->hasLieuEditable()) {
                                    $col['mentionVal'] = $detail->lieu;
                                } else {
                                    $col['mentionVal'] = $detail->denomination;
                                }
                                
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
                                if (count($cepage->detail->toArray(true, false)) < 2 && $this->destinataire == self::DEST_CIVA) {
                                    $col['exploitant']['L15'] = $cepage->getVolumeRevendique() - $cepage->getTotalVolumeAcheteurs('negoces') - $cepage->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                    if ($col['exploitant']['L15'] < 0) {
                                        $col['exploitant']['L15'] = 0;
                                    }
                                    $col['exploitant']['L16'] = $cepage->getUsagesIndustriels(); //DPLC
                                } else {
                                    $col['exploitant']['L15'] = $detail->getVolumeRevendique() - $detail->getTotalVolumeAcheteurs('negoces') - $detail->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                    if ($this->destinataire != self::DEST_DOUANE && $col['exploitant']['L15'] < 0) {
                                        $col['exploitant']['L15'] = 0;
                                    }
                                    $col['exploitant']['L16'] = $detail->getUsagesIndustriels(); //DPLC
                                }

<<<<<<< HEAD
                                if(is_null($col['exploitant']['L16'])) {
                                    $col['exploitant']['L16'] = 0;
                                }
=======

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
                                    if (count($cepage->detail->toArray(true, false)) < 2 && $this->destinataire == self::DEST_CIVA) {
                                        $col['exploitant']['L15'] = $cepage->getVolumeRevendique() - $cepage->getTotalVolumeAcheteurs('negoces') - $cepage->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                        if ($col['exploitant']['L15'] < 0) {
                                            $col['exploitant']['L15'] = 0;
                                        }
                                        $col['exploitant']['L16'] = $cepage->getUsagesIndustriels(); //DPLC
                                    } else {
                                        $col['exploitant']['L15'] = $detail->getVolumeRevendique() - $detail->getTotalVolumeAcheteurs('negoces') - $detail->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                        if ($this->destinataire != self::DEST_DOUANE && $col['exploitant']['L15'] < 0) {
                                            $col['exploitant']['L15'] = 0;
                                        }
                                        $col['exploitant']['L16'] = $detail->getUsagesIndustriels(); //DPLC
                                    }
>>>>>>> d105e46... AJout des balises achat pour les achats faits auprès d'autres viti

                                $col['exploitant']['L17'] = 0; //HS
                                $col['exploitant']['L18'] = 0; //HS
                                $col['exploitant']['L19'] = 0; //HS

                                if ($cepage->getKey() == 'cepage_RB' && $appellation->getKey() == 'appellation_CREMANT') {
                                    $col['exploitant']['L14'] = $detail->volume;
                                    $col['exploitant']['L15'] = 0;
                                } elseif($appellation->getKey() == 'appellation_VINTABLE') {
                                    $l14 = $detail->volume ;
                                    if ($l14 < 0) {
                                        $l14 = 0;
                                    }
                                    $col['exploitant']['L14'] = $l14;
                                    if ($this->destinataire == self::DEST_CIVA) {
                                        $col['exploitant']['L14'] = $detail->volume;
                                    }
                                    $col['exploitant']['L15'] = 0;

                                    if($this->destinataire == self::DEST_DOUANE && round($cepage->getTotalVolumeAcheteurs('negoces'), 2) == $cepage->getTotalVolume()) {
                                        $col['exploitant']['L14'] = 0;
                                    }
                                }

                                uksort($col['exploitant'], 'exportDRXml::sortXML');

                                if ($detail->exist('motif_non_recolte') && $detail->motif_non_recolte) {
                                    $col['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                                } elseif(!$detail->volume || $detail->volume == 0) {
                                    if ($appellation->getKey() == 'appellation_ALSACEBLANC' &&
                                        $dr->recolte->getNoeudAppellations()->exist('appellation_ALSACEBLANC') &&
                                        $dr->recolte->getNoeudAppellations()->get('appellation_ALSACEBLANC')->lieu->couleur->exist('cepage_ED') &&
                                        $dr->recolte->getNoeudAppellations()->get('appellation_ALSACEBLANC')->lieu->couleur->get('cepage_ED')->getTotalVolume() > 0) {
                                        $col['motifSurfZero'] = 'AE';
                                    } else {
                                        $col['motifSurfZero'] = 'PC';
                                    }
                                    if ($appellation->getKey() == 'appellation_LIEUDIT' &&
                                        $dr->recolte->getNoeudAppellations()->exist('appellation_LIEUDIT') &&
                                        $dr->recolte->getNoeudAppellations()->get('appellation_LIEUDIT')->lieu->couleur->exist('cepage_ED') &&
                                        $dr->recolte->getNoeudAppellations()->get('appellation_LIEUDIT')->lieu->couleur->get('cepage_ED')->getTotalVolume() > 0) {
                                        $col['motifSurfZero'] = 'AE';
                                    } else {
                                        $col['motifSurfZero'] = 'PC';
                                    }
                                }

                                if (!($object->getTotalVolume() > 0)) {
                                    if ($detail->exist('motif_non_recolte') && $detail->motif_non_recolte) {
                                        $total['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                                    } elseif(!isset($total['motifSurfZero'])) {
                                        $total['motifSurfZero'] = 'PC';
                                    }
                                }

                                if ($cepage->getKey() == 'cepage_RB' && $appellation->getKey() == 'appellation_CREMANT') {
                                    unset($col['L3'], $col['L4'], $col['mentionVal']);
                                    $colass = $col;
                                } else {
                                    $cols[$detail->vtsgn][] = $col;
                                }
                            }

                            if ($this->destinataire == self::DEST_DOUANE) {
                                $col_final = null;
                                foreach($cols as $vtsgn => $groupe_cols) {
                                    if (count($groupe_cols) > 0) {
                                        $col_final = $groupe_cols[0];
                                        unset($groupe_cols[0]);
                                    }
                                    foreach($groupe_cols as $col) {
                                        unset($col_final['mentionVal']);
                                        if ($cepage->getTotalVolume() != 0) {
                                            unset($col_final['motifSurfZero']);
                                        }
                                        $col_final = $this->sumColonnes($col_final, $col);
                                    }

                                    if(!$vtsgn) {
                                        if($cepage->getDplc() > $cepage->getLies()) {
                                            $col_final['exploitant']['L15'] = $col_final['exploitant']['L15'] + $cepage->getLies() - $cepage->getDplc();
                                            $col_final['exploitant']['L16'] = $cepage->getDplc();
                                        }

                                        if($cepage->getDplc() > $cepage->getLies() && $cepage->getLies() && count($cepage->detail->toArray(true, false)) > 1) {
                                            echo "Warning DPLC > lies et lies > 0 et plusieurs détails : " . $dr->_id . "\n";
                                        }

                                        if ($col_final['exploitant']['L15'] < 0) {
                                            $col_final['exploitant']['L15'] = 0;
                                        }

                                        if($appellation->getKey() == 'appellation_VINTABLE') {
                                            $col_final['exploitant']['L15'] = 0;
                                        }
                                    }

                                    if (in_array($appellation->getKey(), array('appellation_CREMANT'))) {
                                        if ($cepage->getKey() == 'cepage_PN') {
                                            $col_total_cremant_rose = $this->sumColonnes($col_total_cremant_rose, $col_final);
                                            unset($col_total_cremant_rose['mentionVal']);
                                            if($col_total_cremant_rose['exploitant']['L5'] > 0) {
                                                unset($col_total_cremant_rose['motifSurfZero']);
                                            }
                                        } else {
                                            $col_total_cremant_blanc = $this->sumColonnes($col_total_cremant_blanc, $col_final);
                                            unset($col_total_cremant_blanc['mentionVal']);
                                            if($col_total_cremant_blanc['exploitant']['L5'] > 0) {
                                                unset($col_total_cremant_blanc['motifSurfZero']);
                                            }
                                        }
                                    } else {
                                        uksort($col_final['exploitant'], 'exportDRXml::sortXML');
                                        $xml[] = $col_final;
                                    }
                                }
                            } elseif($this->destinataire == self::DEST_CIVA) {
                                foreach($cols as $groupe_cols) {
                                    foreach($groupe_cols as $col) {
                                        $xml[] = $col;    
                                    }
                                }
                            }
                        }
                    }

                    uksort($total['exploitant'], 'exportDRXml::sortXML');

                    if ($colass) {
                        $total['colonneAss'] = $colass;
                    }

                    if ($this->destinataire == self::DEST_DOUANE) {
                        if($appellation->getKey() == 'appellation_CREMANT') {
                            if ($col_total_cremant_blanc) {
                                $col_total_cremant_blanc['L1'] = '1B001M';
                                uksort($col_total_cremant_blanc['exploitant'], 'exportDRXml::sortXML');
                                $xml[] = $col_total_cremant_blanc;
                            }
                            if ($col_total_cremant_rose) {
                                $col_total_cremant_rose['L1'] = '1S001M';
                                uksort($col_total_cremant_rose['exploitant'], 'exportDRXml::sortXML');
                                $xml[] = $col_total_cremant_rose;
                            }
                        }
                        if (!in_array($appellation->getKey(), array('appellation_GRDCRU', 'appellation_VINTABLE')) && ($mention->getKey() == 'mention')) {
                            $xml[] = $total;
                        }
                    }

                }
            }
        }
        $this->content = $this->getPartial('dr_export/xml', array('dr' => $dr, 'colonnes' => $xml, 'achats' => $baliseachat, 'destinataire' => $this->destinataire));
    }

    protected function sumColonnes($cols, $col) {
        if (is_null($cols)) {
            $cols = $col;

            return $cols;
        }
        $cols['L4'] += $col['L4'];
        foreach($cols['exploitant'] as $expl_key => $value) {
            if(is_array($value)) {
                if(array_key_exists($expl_key, $col['exploitant'])) {
                    $cols['exploitant'][$expl_key]['volume'] += $col['exploitant'][$expl_key]['volume'];
                }
            } else {
                $cols['exploitant'][$expl_key] += $col['exploitant'][$expl_key];
            }
        }
        foreach($col['exploitant'] as $expl_key => $value) {
            if(is_array($value)) {
                if(!array_key_exists($expl_key, $cols['exploitant'])) {
                    $cols['exploitant'][$expl_key] = $value;
                }
            }
        }

        return $cols;
    }
}
