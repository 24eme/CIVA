<?php

class SVClient extends acCouchdbClient {

    const TYPE_SV11 = "SV11";
    const TYPE_SV12 = "SV12";

    const CSV_ERROR_ACHETEUR  = "CSV_ERROR_ACHETEUR";
    const CSV_ERROR_APPORTEUR = "CSV_ERROR_APPORTEUR";
    const CSV_ERROR_PRODUIT   = "CSV_ERROR_PRODUIT";
    const CSV_ERROR_VOLUME    = "CSV_ERROR_VOLUME";
    const CSV_ERROR_VOLUME_REBECHE = "CSV_ERROR_VOLUME_REBECHE";
    const CSV_ERROR_SUPERFICIE  = "CSV_ERROR_SUPERFICIE";
    const CSV_ERROR_QUANTITE = "CSV_ERROR_QUANTITE";
    const CSV_ERROR_VOLUME_REVENDIQUE_SV11 = "CSV_ERROR_VOLUME_REVENDIQUE_SV11";
    const CSV_ERROR_VOLUME_REVENDIQUE_SV12 = "CSV_ERROR_VOLUME_REVENDIQUE_SV12";

    public static function getInstance()
    {
        return acCouchdbManager::getClient("SV");
    }

    public function findByIdentifiantAndCampagne($identifiantEtablissement, $campagne)
    {
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiantEtablissement);

        return SVClient::getInstance()->find(SVClient::getTypeByEtablissement($etablissement).'-'.$etablissement->cvi.'-'.$campagne);
    }

    public function getAll($campagne = null)
    {
        $start = "0000";
        $end   = "9999";

        if ($campagne) {
            $start = $end = $campagne;
        }

        return $this->startkey("SV11-0000000000-".$start)->endkey("SV12-9999999999-".$end)->execute();
    }

    public static function getTypeByEtablissement($etablissement) {
        $type = null;

        if($etablissement->famille == EtablissementFamilles::FAMILLE_COOPERATIVE) {
            $type = 'SV11';
        } elseif($etablissement->famille == EtablissementFamilles::FAMILLE_NEGOCIANT) {
            $type = 'SV12';
        } else {
            throw new Exception("La famille ".$etablissement->famille." ne peut pas faire de document de production");
        }

        return $type;
    }

    public function isTeledeclarationOuverte()
    {
        $now = new DateTimeImmutable();
        $ouverture = new DateTimeImmutable(sfConfig::get('app_production_date_ouverture'));
        $fermeture = new DateTimeImmutable(sfConfig::get('app_production_date_fermeture'));

        return $now > $ouverture && $now < $fermeture;
    }

    public function createSV($identifiantEtablissement, $campagne) {
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiantEtablissement);

        $sv = new SV();
        $sv->identifiant = $etablissement->cvi;
        $sv->id_etablissement = $etablissement->_id;
        $sv->type = SVClient::getTypeByEtablissement($etablissement);
        $sv->periode = $campagne;
        $sv->campagne = ''.$campagne.'-'.($campagne+1);
        $sv->constructId();
        $sv->storeDeclarant();
        $sv->storeStorage();

        return $sv;
    }

    public function createFromDR($identifiantEtablissement, $campagne)
    {
        $sv = $this->createSV($identifiantEtablissement, $campagne);

        $cvi_acheteur = $sv->identifiant;
        if(!$cvi_acheteur) {
            return;
        }
        $drAcheteurType = 'negoces';
        if($sv->getType() == SVClient::TYPE_SV11) {
            $drAcheteurType = 'cooperatives';
        }

        $drs = DRClient::getInstance()->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach ($drs as $id => $doc) {
            $dr = DRClient::getInstance()->find($id);
            foreach ($dr->getProduits() as $cepage) {
                if($cepage->getAppellation()->getKey() == "appellation_CREMANT" && strpos($cepage->getCepage()->getKey(), "cepage_RB") !== false) {
                    continue;
                }
                $hasRebeches = $cepage->getCouleur()->exist('cepage_RB') && $cepage->getCouleur()->get('cepage_RB')->getVolumeAcheteur($cvi_acheteur, $drAcheteurType, false);

                $hash = HashMapper::convert($cepage->getHash());
                if($cepage->getAppellation()->getKey() == "appellation_CREMANT" && $cepage->getKey() == "cepage_PN") {
                    $hash = HashMapper::convert($cepage->getCouleur()->getHash()).'/cepages/RS';
                } elseif($cepage->getAppellation()->getKey() == "appellation_CREMANT" && strpos($cepage->getKey(), "cepage_RB") === false) {
                    $hash = HashMapper::convert($cepage->getCouleur()->getHash()).'/cepages/BL';
                }

                $svDetails = [];
                $volumes = [];
                foreach ($cepage->getProduitsDetails() as $detail) {
                    $volumeAcheteur = $detail->getVolumeByAcheteur($cvi_acheteur, $drAcheteurType);
                    if(!$volumeAcheteur) {
                        continue;
                    }
                    $denomination = $this->formatDenomination($detail->denomination);
                    if($detail->lieu) {
                        $denomination = strtoupper(trim(preg_replace('/[ ]+/', ' ', $detail->lieu)));
                    }

                    $svDetail = $sv->addProduit($dr->identifiant, $hash, $denomination);

                    $svDetails[$svDetail->getHash()] = $svDetail;
                    if(!isset($volumes[$svDetail->getHash()])) {
                        $volumes[$svDetail->getHash()] = 0;
                    }
                    $volumes[$svDetail->getHash()] += $volumeAcheteur;

                    if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/RS") !== false && $hasRebeches) {
                        $sv->addProduit($dr->identifiant, str_replace("/cepages/RS", "/cepages/RBRS", $hash));
                    }

                    if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/BL") !== false && $hasRebeches) {
                        $sv->addProduit($dr->identifiant, str_replace("/cepages/BL", "/cepages/RBBL", $hash));
                    }

                    if($volumeAcheteur != $detail->volume) {
                        $svDetail->superficie_recolte = null;
                        continue;
                    }

                    $svDetail->superficie_recolte += $detail->superficie;
                }

                foreach($svDetails as $svKey => $svDetail) {
                    if(!is_null($svDetail->superficie_recolte)) {
                        continue;
                    }
                    if($cepage->getVolumeAcheteur($cvi_acheteur, $drAcheteurType) == $volumes[$svKey]) {
                        $svDetail->superficie_recolte = $cepage->getTotalSuperficieVendusByCvi($drAcheteurType, $cvi_acheteur);
                    }

                    if($cepage->getCouleur()->getVolumeAcheteur($cvi_acheteur, $drAcheteurType) == $volumes[$svKey]) {
                        $svDetail->superficie_recolte = $cepage->getCouleur()->getTotalSuperficieVendusByCvi($drAcheteurType, $cvi_acheteur);
                    }

                    if($cepage->getLieu()->getVolumeAcheteur($cvi_acheteur, $drAcheteurType) == $volumes[$svKey]) {
                        $svDetail->superficie_recolte = $cepage->getLieu()->getTotalSuperficieVendusByCvi($drAcheteurType, $cvi_acheteur);
                    }
                }

                if($cepage->getVolumeAcheteur($cvi_acheteur, 'mouts')) {
                    $svProduit = $sv->addProduit($dr->identifiant, $hash);
                    if (!$svProduit->exist('volume_mouts')) {
                        $svProduit->add('volume_mouts');
                        $svProduit->add('volume_mouts_revendique');
                        $svProduit->add('superficie_mouts');
                    }
                    $svProduit->volume_mouts += $cepage->getVolumeAcheteur($cvi_acheteur, 'mouts');
                }
            }
        }

        return $sv;
    }

    protected function formatDenomination($denomination) {
        $denoms = array();

        if(preg_match('/VI?E?I?LLES?[ ]*VIGNES?/i', $denomination)) {
            $denoms[] = 'VIEILLES VIGNES';
        }

        if(preg_match('/(-| |^)AB(-| |$)/i', $denomination)) {
            $denoms[] = 'AB';
        }

        if(preg_match('/(-| |^)BIO(-| |$)/i', $denomination)) {
            $denoms[] = 'BIO';
        }

        if(preg_match('/(-| |^)HVE(-| |$)/i', $denomination)) {
            $denoms[] = 'BIO';
        }

        if(preg_match('/AUXERROIS/i', $denomination)) {
            $denoms[] = 'AUXERROIS';
        }

	   if(preg_match('/SAINTE[ -]*HUNE/i', $denomination)) {
            $denoms[] = 'CLOS SAINTE HUNE';
        }

        return implode(" ", $denoms);
    }

    public function identifyProductCSV($line) {

        return CsvFileAcheteur::identifyProductCSV($line);
    }

    public function createFromCSV($identifiantEtablissement, $campagne, CsvFileAcheteur $csv) {
        $sv = $this->createSV($identifiantEtablissement, $campagne);

        $lines = $csv->getCsv();
        uasort($lines, function ($a, $b) {
            return $a[CsvFileAcheteur::CSV_RECOLTANT_CVI] > $b[CsvFileAcheteur::CSV_RECOLTANT_CVI];
        });

        foreach ($lines as $line) {
            if(!preg_match('/^[0-9]+/', $line[0])) {
                continue;
            }

            if (preg_match('/JEUNES +VIGNES/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            if (preg_match('/JUS DE RAISIN/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            if (strpos(strtoupper($line[CsvFileAcheteur::CSV_APPELLATION]), 'LIES') !== false || strpos(strtoupper($line[CsvFileAcheteur::CSV_APPELLATION]), 'BOURBES') !== false) {
                $sv->lies += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                continue;
            }

            $apporteur = EtablissementClient::getInstance()->findByCvi($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]);
            $produit = CsvFileAcheteur::identifyProductCSV($line);

            $produit = $sv->addProduit($apporteur->identifiant, $produit->getHash(), $line[CsvFileAcheteur::CSV_DENOMINATION]);

            if (strpos(KeyInflector::slugify($line[CsvFileAcheteur::CSV_APPELLATION]), 'MOUTS') !== false) {
                $produit->add('volume_mouts');
                $produit->add('volume_mouts_revendique');
                $produit->add('superficie_mouts');
                $produit->volume_mouts += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                $produit->volume_mouts_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT]);
                $produit->superficie_mouts += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);

                continue;
            }

            if (strpos($produit->getHash(), 'cepages/RB') !== false) {
                $produit->volume_recolte += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                continue;
            }

            $produit->superficie_recolte += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);

            if($sv->getType() == SVClient::TYPE_SV12) {
                $produit->quantite_recolte += (int) $line[CsvFileAcheteur::CSV_SV_QUANTITE_VF];
                $produit->volume_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT]);
            }

            if($sv->getType() == SVClient::TYPE_SV11) {
                $produit->volume_recolte += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                $produit->volume_detruit += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_DPLC]);
                $produit->vci += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VCI]);
                $produit->volume_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT]);
            }
        }

        $sv->storeAttachment($csv->getFileName(), "text/csv", md5_file($csv->getFileName()));
        return $sv;
    }

    protected function recodeNumber($value) {

        return round(str_replace(",", ".", $value)*1, 2);
    }

    public function checkCSV(CsvFileAcheteur $csv, $identifiant, $campagne, $type)
    {
        $check = [];
        $i = 0;

        foreach ($csv->getCsv() as $line) {
            $i++;

            if ($line[CsvFileAcheteur::CSV_ACHETEUR_CVI] && $line[CsvFileAcheteur::CSV_ACHETEUR_CVI] !== $identifiant) {
                $check[self::CSV_ERROR_ACHETEUR][] = [$i];
                continue;
            }

            if (preg_match('/JEUNES +VIGNES/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            if (preg_match('/JUS DE RAISIN/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            $apporteur = EtablissementClient::getInstance()->findByCvi($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]);

            if(! $apporteur && strpos(strtoupper($line[CsvFileAcheteur::CSV_APPELLATION]), 'LIE') !== 0) {
                $check[self::CSV_ERROR_APPORTEUR][] = [$i];
                continue;
            }

            if (strpos(strtoupper($line[CsvFileAcheteur::CSV_APPELLATION]), 'LIES') === 0) {
                if ($line[CsvFileAcheteur::CSV_SV_VOLUME_VF] <= 0) {
                    $check[self::CSV_ERROR_VOLUME][] = [$i];
                }
                continue;
            }

            $produit = CsvFileAcheteur::identifyProductCSV($line);

            if(! $produit) {
                $check[self::CSV_ERROR_PRODUIT][] = [$i];
                continue;
            }

            if (strpos($produit->getHash(), 'cepages/RB') !== false && round($line[CsvFileAcheteur::CSV_SV_VOLUME_VF], 2) <= 0) {
                $check[self::CSV_ERROR_VOLUME_REBECHE][] = [$i];
            }

            if (strpos($produit->getHash(), 'cepages/RB') !== false) {
                continue;
            }

            if ($line[CsvFileAcheteur::CSV_SUPERFICIE] <= 0) {
                $check[self::CSV_ERROR_SUPERFICIE][] = [$i];
            }

            if($type == SVClient::TYPE_SV11) {
                if ($line[CsvFileAcheteur::CSV_SV_VOLUME_VF] <= 0) {
                    $check[self::CSV_ERROR_VOLUME][] = [$i];
                }
                $volume = $line[CsvFileAcheteur::CSV_SV_VOLUME_VF];
                if (isset($line[CsvFileAcheteur::CSV_SV_VOLUME_DPLC])) {
                    $volume -= (float) $line[CsvFileAcheteur::CSV_SV_VOLUME_DPLC];
                }
                if (isset($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT])) {
                    $volume -= (float) $line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT];
                }
                if (round($volume, 2) != 0) {
                    $check[self::CSV_ERROR_VOLUME_REVENDIQUE_SV11][] = [$i];
                }
                if (isset($line[CsvFileAcheteur::CSV_SV_VOLUME_VCI]) && ((float) $line[CsvFileAcheteur::CSV_SV_VOLUME_VCI]) > ((float) $line[CsvFileAcheteur::CSV_SV_VOLUME_DPLC])) {
                    $check[self::CSV_ERROR_VOLUME_REVENDIQUE_SV11][] = [$i];
                }
            }

            if($type == SVClient::TYPE_SV12) {
                if ($line[CsvFileAcheteur::CSV_SV_QUANTITE_VF] <= 0 && strpos(KeyInflector::slugify($line[CsvFileAcheteur::CSV_APPELLATION]), 'MOUTS') === false) {
                    $check[self::CSV_ERROR_QUANTITE][] = [$i];
                }
                if ($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT] <= 0) {
                    $check[self::CSV_ERROR_VOLUME_REVENDIQUE_SV12][] = [$i];
                }
            }
        }

        return $check;
    }

    public function getEtablissement($societe) {
        foreach($societe->getEtablissementsObject(true, true) as $etablissement) {

            if($etablissement->hasDroit(Roles::TELEDECLARATION_DR_ACHETEUR)) {

              return $etablissement;
            }
        }

        return null;
    }

}
