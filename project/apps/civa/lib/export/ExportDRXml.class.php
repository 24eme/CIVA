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
            $key = self::$type2douane[$type].'_'.$cvi;
            if(!array_key_exists($key, $xml)) {
                $item = array('numCvi' => $cvi, 'volume' => 0);
            } else {
                $item = $xml[$key];
            }
            $item['volume'] += $volume;
            if($type == 'negoces' && $this->destinataire != self::DEST_CIVA) {
                $item['volume'] = $item['volume'] - $obj->getTotalDontVciVendusByCvi($type, $cvi);
            }

            $xml[$key] = $item;
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

    protected function getColTotal($object, $total) {
        $volume_revendique = $object->getVolumeRevendique();
        $usages_industriels = $object->getUsagesIndustriels();
        $vci = $object->getTotalVci();

        $total['L1'] = $this->getCodeDouane($object);

        $total['L3'] = 'B';
        if(!array_key_exists('L4', $total)) {
            $total['L4'] = 0;
        }
        $total['L4'] += $object->getTotalSuperficie();

        if(!array_key_exists('exploitant', $total)) {
            $total['exploitant'] = array();
        }

        if(!array_key_exists('L5', $total['exploitant'])) {
            $total['exploitant']['L5'] = 0;
        }
        $total['exploitant']['L5'] += $object->getTotalVolume();

        $this->setAcheteursForXml($total['exploitant'], $object, 'negoces');
        $this->setAcheteursForXml($total['exploitant'], $object, 'mouts');
        $this->setAcheteursForXml($total['exploitant'], $object, 'cooperatives');

        if(!array_key_exists('L9', $total['exploitant'])) {
            $total['exploitant']['L9'] = 0;
        }
        $total['exploitant']['L9'] += $object->getTotalCaveParticuliere() + $object->getTotalDontVciVendusByType('negoces');
        if(($object->getTotalDontVciVendusByType('negoces') != 0) && $this->destinataire == self::DEST_CIVA){
          $total['exploitant']['L9'] = $object->getTotalCaveParticuliere();
        }

        if(!array_key_exists('L10', $total['exploitant'])) {
            $total['exploitant']['L10'] = 0;
        }
        $total['exploitant']['L10'] += $object->getTotalCaveParticuliere() + $object->getTotalVolumeAcheteurs('cooperatives') + $object->getTotalDontVciVendusByType('negoces'); //Volume revendique non negoces
        if(($object->getTotalDontVciVendusByType('negoces') != 0) && $this->destinataire == self::DEST_CIVA){
          $total['exploitant']['L10'] = $object->getTotalCaveParticuliere();
        }

        $total['exploitant']['L11'] = 0; //HS
        $total['exploitant']['L12'] = 0; //HS
        $total['exploitant']['L13'] = 0; //HS
        $total['exploitant']['L14'] = 0; //Vin de table + Rebeches
        $l15 = $volume_revendique - $object->getTotalVolumeAcheteurs('negoces') - $object->getTotalVolumeAcheteurs('mouts');
        if ($l15 < 0) {
            $l15 = 0;
        }
        if(!array_key_exists('L15', $total['exploitant'])) {
            $total['exploitant']['L15'] = 0;
        }
        $total['exploitant']['L15'] += $l15; //Volume revendique
        // Modifications suite au retour des douanes le total dplc total et celui du rendement appellation et plus de la somme pour les alsace blanc
        if(!array_key_exists('L16', $total['exploitant'])) {
            $total['exploitant']['L16'] = 0;
        }
        $total['exploitant']['L16'] += $usages_industriels + $vci; //DPLC
        $total['exploitant']['L17'] = 0; //HS
        $total['exploitant']['L18'] = 0; //HS
        $total['exploitant']['L19'] = $vci; //HS

        return $total;
    }

    protected function create($dr) {
        $xml = array();
        $baliseachat = array();
        foreach ($dr->recolte->getConfig()->getArrayAppellations() as $appellationConfig) {
            if (!$dr->exist(HashMapper::inverse($appellationConfig->getHash()))) {
                continue;
            }
            $appellation = $dr->get(HashMapper::inverse($appellationConfig->getHash()));

            $totals = array();
            foreach ($appellationConfig->getMentions() as $mentionConfig) {
                if (!$dr->exist(HashMapper::inverse($mentionConfig->getHash()))) {
                    continue;
                }
                $mention = $dr->get(HashMapper::inverse($mentionConfig->getHash()));

                foreach ($mentionConfig->getLieux() as $lieuConfig) {

                    if (!$dr->exist(HashMapper::inverse($lieuConfig->getHash()))) {
                        continue;
                    }
                    $lieu = $dr->get(HashMapper::inverse($lieuConfig->getHash()));

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

                    foreach($lieuConfig->getCouleurs() as $couleurConfig) {
                        if (!$dr->exist(HashMapper::inverse($couleurConfig->getHash()))) {
                            continue;
                        }
                        $couleur = $dr->get(HashMapper::inverse($couleurConfig->getHash()));

                        $object = $lieu;
                        $objectChanged = true;
                        if ($lieuConfig->hasManyCouleur()) {
                            $object = $couleur;
                            $objectChanged = true;
                        }

                        if($this->destinataire == self::DEST_DOUANE) {
                            if($appellation->getKey() == 'appellation_CREMANT') {
                                $col_total_cremant_blanc = null;
                                $col_total_cremant_rose = null;
                            }
                        }

                        $colass = null;

                        $cepagesConfig = array();

                        foreach($couleurConfig->getCepages() as $cepConfig) {
                            if ($cepConfig->exist('attributs') && $cepConfig->attributs->exist('no_dr') && $cepConfig->attributs->no_dr) {
                              continue;
                            }

                            $cepagesConfig[$cepConfig->getHash()] = $cepConfig;
                        }

                        if ($this->destinataire == self::DEST_DOUANE &&
                            count($cepagesConfig) == 1 &&
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
                                    }
                                }
                            }
                        } else {
                            foreach ($couleurConfig->getCepages() as $cepageConfig) {
                                if (!$dr->exist(HashMapper::inverse($cepageConfig->getHash()))) {
                                    continue;
                                }
                                $cepage = $dr->get(HashMapper::inverse($cepageConfig->getHash()));

                                // -------------- DEBUT ---------------

                                if($appellation->getKey() == 'appellation_ALSACEBLANC' && $cepage->hasRecapitulatif()) {
                                    $object = $cepage;
                                    $objectChanged = true;
                                }

                                if($objectChanged) {
                                    $total = array();
                                    if(array_key_exists($this->getCodeDouane($object), $totals)) {
                                        $total = $totals[$this->getCodeDouane($object)];
                                    }

                                    $total = $this->getColTotal($object, $total);
                                }

                                // ----------- FIN -----------

                                $objectChanged = false;

                                $cols = array();
                                if(!$object instanceof DRRecolteCepage) {
                                foreach ($cepage->detail as $detail) {

                                    if ($detail->exist('attributs') && $detail->attributs->exist('no_dr') && $detail->attributs->no_dr) {
                                      continue;
                                    }

                                    $col = array();

                                    $col['L1'] = $this->getCodeDouane($detail);

                                    $col['L3'] = 'B';
                                    if ($appellationConfig->hasLieuEditable()) {
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

                                    $vci_negoce = $detail->getTotalVciAcheteur('negoces');

                                    $col['exploitant']['L9'] = (!is_null($vci_negoce)) ? $detail->cave_particuliere + $vci_negoce : 0; //Volume revendique sur place
                                    if(($cepage->getTotalVolumeAcheteurs('negoces') != 0) && $this->destinataire == self::DEST_CIVA){
                                      $col['exploitant']['L9'] = (!is_null($vci_negoce)) ? $detail->cave_particuliere + 0 : 0;
                                    }

                                    $col['exploitant']['L10'] = (!is_null($vci_negoce)) ? $detail->cave_particuliere + $vci_negoce + $detail->getTotalVolumeAcheteurs('cooperatives') : 0;
                                    if(($cepage->getTotalVolumeAcheteurs('negoces') != 0) && $this->destinataire == self::DEST_CIVA){
                                      $col['exploitant']['L10'] = (!is_null($vci_negoce)) ? $detail->cave_particuliere + $detail->getTotalVolumeAcheteurs('cooperatives') : 0;
                                    }

                                    $col['exploitant']['L11'] = 0; //HS
                                    $col['exploitant']['L12'] = 0; //HS
                                    $col['exploitant']['L13'] = 0; //HS
                                    $col['exploitant']['L14'] = 0; //Vin de table + Rebeches
                                    if (count($cepage->detail->toArray(true, false)) < 2 && $this->destinataire == self::DEST_CIVA) {
                                        $col['exploitant']['L15'] = $cepage->getVolumeRevendique() - $cepage->getTotalVolumeAcheteurs('negoces') - $cepage->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                        if ($col['exploitant']['L15'] < 0) {
                                            $col['exploitant']['L15'] = 0;
                                        }
                                        $col['exploitant']['L16'] = $cepage->getUsagesIndustriels();

                                        if($cepage->getTotalVolumeAcheteurs('negoces') == 0){
                                          $col['exploitant']['L16'] += $cepage->getTotalVci();
                                        }
                                        $col['exploitant']['L19'] = $cepage->getTotalVci();
                                    } elseif(count($cepage->detail->toArray(true, false)) < 2 && $this->destinataire == self::DEST_DOUANE && $mention->getKey() != 'mention') {
                                        $col['exploitant']['L15'] = $cepage->getVolumeRevendique() - $cepage->getTotalVolumeAcheteurs('negoces') - $cepage->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                        if ($col['exploitant']['L15'] < 0) {
                                            $col['exploitant']['L15'] = 0;
                                        }
                                        $col['exploitant']['L16'] = $cepage->getUsagesIndustriels() + $cepage->getTotalVci(); //DPLC
                                        $col['exploitant']['L19'] = $cepage->getTotalVci();
                                    } else {
                                        $col['exploitant']['L15'] = $detail->getVolumeRevendique() - $detail->getTotalVolumeAcheteurs('negoces') - $detail->getTotalVolumeAcheteurs('mouts'); //Volume revendique
                                        if ($this->destinataire != self::DEST_DOUANE && $col['exploitant']['L15'] < 0) {
                                            $col['exploitant']['L15'] = 0;
                                        }
                                        $col['exploitant']['L16'] = $detail->getUsagesIndustriels() + $detail->getTotalVci(); //DPLC
                                        $col['exploitant']['L19'] = $detail->getTotalVci();
                                    }

                                    if(is_null($col['exploitant']['L16'])) {
                                        $col['exploitant']['L16'] = 0;
                                    }

                                    $col['exploitant']['L17'] = 0; //HS
                                    $col['exploitant']['L18'] = 0; //HS
                                    if(is_null($col['exploitant']['L19'])) {
                                        $col['exploitant']['L19'] = 0;
                                    }

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

                                } else {
                                    $totals[$total['L1']] = $total;
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
                                                $col_final['exploitant']['L16'] = $cepage->getDplcWithVci();
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
                        }
                        if (!in_array($appellation->getKey(), array('appellation_GRDCRU', 'appellation_VINTABLE')) && ($mention->getKey() == 'mention') && $this->destinataire == self::DEST_DOUANE) {
                            $xml[] = $total;
                        }

                        if($this->destinataire == self::DEST_CIVA) {
                            $totals[$total['L1']] = $total;
                        }
                    }

                    if($this->destinataire == self::DEST_CIVA && $appellation->getKey() == 'appellation_ALSACEBLANC' && !$lieu->hasRecapitulatif()) {
                        $totals[$this->getCodeDouane($lieu)] = $this->getColTotal($lieu, array());
                    }
                }

            }
            if(!in_array($appellation->getKey(), array('appellation_GRDCRU', 'appellation_VINTABLE')) && $this->destinataire == self::DEST_CIVA) {
                foreach($totals as $total) {
                    $xml[] = $total;
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

    public function getCodeDouane($noeud) {

        if ($noeud instanceof DRRecolteCepageDetail && $noeud->getCepage()->getAppellation()->getKey() == 'appellation_VINTABLE' && $noeud->getCepage()->getKey() == 'cepage_BL') {

            return "4B999";
        }
        if ($noeud instanceof DRRecolteCepageDetail && $noeud->getCepage()->getAppellation()->getKey() == 'appellation_VINTABLE' && $noeud->getCepage()->getKey() == 'cepage_RG') {

            return "4R999";
        }
        if ($noeud instanceof DRRecolteCepageDetail && $noeud->getCepage()->getAppellation()->getKey() == 'appellation_VINTABLE' && $noeud->getCepage()->getKey() == 'cepage_RS') {

            return "4S999";
        }

        if($noeud instanceof DRRecolteLieu && $noeud->getAppellation()->getKey() == "appellation_CREMANT") {

            return "1B001MST";
        }

        if ($this->destinataire == self::DEST_DOUANE) {
          return $noeud->getCodeDouane();
        }

        if($noeud instanceof DRRecolteCepageDetail && ($noeud->getParent()->getParent()->getAppellation()->getKey() == "appellation_COMMUNALE" || $noeud->getParent()->getParent()->getAppellation()->getKey() == "appellation_LIEUDIT")) {
            $codeDouane = $noeud->getCodeDouane();
            if (strlen($codeDouane) > 6) {
              return $codeDouane;
            }
            $codeCepage = $noeud->getParent()->getParent()->getConfig()->code;
            if ($codeCepage == "GW" && $codeDouane == "1B065S"){
              return $codeDouane."01";
            }elseif ($codeCepage == "GW"){
                return $codeDouane." 1";
            }elseif ($codeCepage == "RI") {
              return $codeDouane." 4";
            }elseif ($codeCepage == "SY" && ($codeDouane == "1B056S" || $codeDouane == "1B055S")) {
              return $codeDouane." 1";
            }elseif ($codeCepage == "SY") {
              return $codeDouane." 8";
            }elseif ($codeCepage == "PR") {
              return $codeDouane." 1";
            }elseif ($codeCepage == "KL") {
              return $codeDouane." 1";
            }elseif ($codeCepage == "PG") {
              return $codeDouane." 3";
            }elseif ($codeCepage == "ED") {
              return $codeDouane."09";
            }
            return $codeDouane."XX";
        }

        return preg_replace("/,.*/", "", $noeud->getCodeDouane());
    }
}
