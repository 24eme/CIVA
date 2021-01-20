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

    public function getNoeudRecap($object) {
        if(preg_match("/appellation_GRDCRU/", $object->getHash()) && preg_match("/cepage/", $object->getHash())) {
            echo $object->getHash()."\n";
            return $object->getCepage();
        }

        $noeudRecap = $object->getNoeudRecapitulatif();
        if(!$noeudRecap && $object instanceof DRRecolteCepageDetail) {
            $noeudRecap = $object->getCepage();
        }
        if(!$noeudRecap) {
            $noeudRecap = $object;

        }

        return $noeudRecap;
    }

    public function getCol($object) {
        $col = array();

        $noeudRecap = $this->getNoeudRecap($object);

        $appellation = $noeudRecap->getAppellation();

        $col['L1'] = $this->getCodeDouane($object);
        $col['L3'] = 'B';

        if($object instanceof DRRecolteCepageDetail && $appellation->getConfig()->hasLieuEditable()) {
            $col['mentionVal'] = $object->lieu;
        } else if($object instanceof DRRecolteCepageDetail) {
            $col['mentionVal'] = $object->denomination;
        } else {
            $acheteursConfig = AcheteurClient::getInstance()->getAcheteurs();
            $denominations = array();
            foreach($object->getProduitsDetails() as $detail) {
                $denomination = $detail->denomination;
                foreach($acheteursConfig as $cvi => $acheteur) {
                    $denomination = str_replace($acheteur['nom'], "", $denomination);
                }
                $denomination = preg_replace("/^ /", "", $denomination);
                $denomination = preg_replace("/ $/", "", $denomination);
                $denominations[$denomination] = $denomination;
            }

            if(count($denominations) == 1) {
                foreach($denominations as $denomination) {
                    $col['mentionVal'] = $denomination;
                }
            }
        }

        if(isset($col['mentionVal'])) {
            $acheteursConfig = AcheteurClient::getInstance()->getAcheteurs();
            $denomination = $col['mentionVal'];
            foreach($acheteursConfig as $cvi => $acheteur) {
                $denomination = str_replace($acheteur['nom'], "", $denomination);
            }
            $denomination = preg_replace("/^ /", "", $denomination);
            $denomination = preg_replace("/ $/", "", $denomination);
            $col['mentionVal'] = $denomination;
        }

        $col['L4'] = $object->getTotalSuperficie();

        $col['exploitant'] = array();
        $col['exploitant']['L5'] = $object->getTotalVolume() ; //Volume total sans lies

        $this->setAcheteursForXml($col['exploitant'], $object, 'negoces');
        $this->setAcheteursForXml($col['exploitant'], $object, 'mouts');
        $this->setAcheteursForXml($col['exploitant'], $object, 'cooperatives');

        $vciNegoce = $this->getRatioRecap($object, "getTotalDontVciVendusByType", array('negoces'));
        $vciMouts = $this->getRatioRecap($object, "getTotalDontVciVendusByType", array('mouts'));

        $col['exploitant']['L9'] += $object->getTotalCaveParticuliere() + $vciNegoce;
        if($this->destinataire == self::DEST_CIVA) {
          $col['exploitant']['L9'] = $object->getTotalCaveParticuliere();
        }

        $col['exploitant']['L10'] += $object->getTotalCaveParticuliere() + $object->getTotalVolumeAcheteurs('cooperatives') + $vciNegoce;
        if($col->destinataire == self::DEST_CIVA){
          $col['exploitant']['L10'] = $object->getTotalCaveParticuliere() + $object->getTotalVolumeAcheteurs('cooperatives');
        }

        $col['exploitant']['L11'] = 0; //HS
        $col['exploitant']['L12'] = 0; //HS
        $col['exploitant']['L13'] = 0; //HS
        $col['exploitant']['L14'] = 0; //Vin de table + Rebeches

        $volumeRevendique = $this->getRatioRecap($object, 'getVolumeRevendique', array());
        $usagesIndustriels = $this->getRatioRecap($object, 'getDepassementGlobal', array());
        $venteNegoce = $this->getRatioRecap($object, "getTotalVolumeAcheteurs", array('negoces'));
        $venteMouts = $this->getRatioRecap($object, "getTotalVolumeAcheteurs", array('mouts'));
        $vci = $this->getRatioRecap($object, "getTotalVci", array());

        $l15 = $volumeRevendique - $venteNegoce - $venteMouts + $vciNegoce + $vciMouts;
        if($l15 < 0) {
            $l15 = 0;
        }
        $l16 = $usagesIndustriels;

        $col['exploitant']['L15'] = $l15; //Volume revendique
        $col['exploitant']['L16'] = $l16;

        $col['exploitant']['L17'] = 0; //HS
        $col['exploitant']['L18'] = 0; //HS
        $col['exploitant']['L19'] = 0;
        if($vci) {
            $col['exploitant']['L19'] = $vci;
        }

        if (preg_match("|cepage_RB|", $object->getHash())) {
            $col['exploitant']['L14'] = $object->getTotalVolume();
            $col['exploitant']['L15'] = 0;
            $col['exploitant']['L16'] = 0;
            $col['exploitant']['L19'] = 0;
        }

        if (preg_match("|appellation_VINTABLE|", $object->getHash())) {
            $l14 = $object->getTotalVolume();
            if ($l14 < 0) {
                $l14 = 0;
            }
            $col['exploitant']['L14'] = $l14;
            if ($this->destinataire == self::DEST_CIVA) {
                $col['exploitant']['L14'] = $object->getTotalVolume();
            }
            $col['exploitant']['L15'] = 0;

            if($this->destinataire == self::DEST_DOUANE && round($object->getTotalVolumeAcheteurs('negoces'), 2) == $object->getTotalVolume()) {
                $col['exploitant']['L14'] = 0;
            }
        }

        uksort($col['exploitant'], 'exportDRXml::sortXML');

        if (!$object->getTotalVolume() && $object->getTotalSuperficie() > 0) {
            foreach($object->getProduitsDetails() as $detail) {
                if ($detail->exist('motif_non_recolte') &&  $detail->motif_non_recolte) {
                    $col['motifSurfZero'] = strtoupper($detail->motif_non_recolte);
                    break;
                }
            }
            if (!isset($col['motifSurfZero']) && $appellation->getKey() == 'appellation_LIEUDIT' &&
                $dr->recolte->getNoeudAppellations()->exist('appellation_LIEUDIT') &&
                $dr->recolte->getNoeudAppellations()->get('appellation_LIEUDIT')->lieu->couleur->exist('cepage_ED') &&
                $dr->recolte->getNoeudAppellations()->get('appellation_LIEUDIT')->lieu->couleur->get('cepage_ED')->getTotalVolume() > 0) {
                $col['motifSurfZero'] = 'AE';
            }

            if(!isset($col['motifSurfZero'])) {
                $col['motifSurfZero'] = 'PC';
            }
        }

        return $col;
    }

    public function getRatioRecap($object, $function, $args) {
        if(preg_match("/cepage_RB/", $object->getHash())) {

            return 0;
        }

        $objectTotal = $this->getNoeudRecap($object);
        $ratio = ($object->getTotalVolume()) ? $objectTotal->getTotalVolume() / $object->getTotalVolume() : 0;

        if(!$ratio) {

            return 0;
        }

        return call_user_func_array(array($objectTotal, $function), $args) / $ratio;
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
                            //$total['mentionVal'] = '';
                            $total = $this->getCol($object);
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

                                    $total = $this->getCol($object);
                                }

                                // ----------- FIN -----------

                                $objectChanged = false;

                                $cols = array();
                                if(!$object instanceof DRRecolteCepage) {
                                    foreach ($cepage->detail as $detail) {
                                        if ($detail->exist('attributs') && $detail->attributs->exist('no_dr') && $detail->attributs->no_dr) {
                                          continue;
                                        }

                                        $col = $this->getCol($detail);

                                        if ($cepage->getKey() == 'cepage_RB' && $appellation->getKey() == 'appellation_CREMANT') {
                                            unset($col['L3'], $col['L4'], $col['mentionVal']);
                                            $colass = $col;
                                        } else {
                                            $cols[$detail->vtsgn][] = $col;
                                        }
                                    }
                                } elseif($this->destinataire == self::DEST_CIVA) {
                                    $totals[$total['L1']] = $total;
                                } elseif($this->destinataire == self::DEST_DOUANE) {
                                    if(!$total['mentionVal']) {
                                        unset($total['mentionVal']);
                                    }
                                    $xml[] = $total;
                                    $total = null;
                                }

                                if ($this->destinataire == self::DEST_DOUANE) {
                                    $col_final = null;
                                    foreach($cols as $vtsgn => $groupe_cols) {
                                        if (count($groupe_cols) > 0) {
                                            $col_final = $groupe_cols[0];
                                            unset($groupe_cols[0]);
                                        }
                                        foreach($groupe_cols as $col) {
                                            if($col['mentionVal'] != $col_final['mentionVal']) {
                                                $col_final['mentionVal'] = null;
                                            }
                                            if ($cepage->getTotalVolume() != 0) {
                                                unset($col_final['motifSurfZero']);
                                            }
                                            $col_final = $this->sumColonnes($col_final, $col);
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
                                            if(!$col_final['mentionVal']) {
                                                unset($col_final['mentionVal']);
                                            }
                                            $xml[] = $col_final;
                                        }
                                    }
                                } elseif($this->destinataire == self::DEST_CIVA) {
                                    foreach($cols as $groupe_cols) {
                                        foreach($groupe_cols as $col) {
                                            if(!$col['mentionVal']) {
                                                unset($col['mentionVal']);
                                            }
                                            $xml[] = $col;
                                        }
                                    }
                                }
                            }
                        }
                        if($total) {
                            uksort($total['exploitant'], 'exportDRXml::sortXML');
                        }
                        if ($colass && $total) {
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
                        if (!in_array($appellation->getKey(), array('appellation_GRDCRU', 'appellation_LIEUDIT', 'appellation_VINTABLE')) && ($mention->getKey() == 'mention') && $this->destinataire == self::DEST_DOUANE && $total) {
                            if(!$total['mentionVal']) {
                                unset($total['mentionVal']);
                            }
                            $xml[] = $total;
                            $total = array();
                        }

                        if($this->destinataire == self::DEST_CIVA) {
                            $totals[$total['L1']] = $total;
                        }
                    }

                    if($this->destinataire == self::DEST_CIVA && $appellation->getKey() == 'appellation_ALSACEBLANC' && !$lieu->hasRecapitulatif()) {
                        $totals[$this->getCodeDouane($lieu)] = $this->getCol($lieu, array());
                    }
                }

            }
            if(!in_array($appellation->getKey(), array('appellation_GRDCRU', 'appellation_VINTABLE')) && $this->destinataire == self::DEST_CIVA) {
                foreach($totals as $total) {
                    if(!$total['mentionVal']) {
                        unset($total['mentionVal']);
                    }
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
        $codeDouane = preg_replace("/,.*/", "", $noeud->getCodeDouane());

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

        if ($noeud instanceof DRRecolteCepageDetail && $noeud->getCepage()->getAppellation()->getKey() == 'appellation_LIEUDIT' && $noeud->getCepage()->getKey() == 'cepage_ED') {

            return "1B070S09";
        }

        if ($this->destinataire == self::DEST_DOUANE) {
          return $codeDouane;
        }

        if($noeud instanceof DRRecolteCepageDetail && ($noeud->getParent()->getParent()->getAppellation()->getKey() == "appellation_COMMUNALE" || $noeud->getParent()->getParent()->getAppellation()->getKey() == "appellation_LIEUDIT")) {
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

        return $codeDouane;
    }
}
