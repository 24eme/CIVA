<?php

class SVClient extends acCouchdbClient {

    const TYPE_SV11 = "SV11";
    const TYPE_SV12 = "SV12";

    const CSV_ERROR_ACHETEUR  = "CSV_ERROR_ACHETEUR";
    const CSV_ERROR_APPORTEUR = "CSV_ERROR_APPORTEUR";
    const CSV_ERROR_PRODUIT   = "CSV_ERROR_PRODUIT";
    const CSV_ERROR_VOLUME    = "CSV_ERROR_VOLUME";

    public static function getInstance()
    {
        return acCouchdbManager::getClient("SV");
    }

    public function findByIdentifiantAndCampagne($identifiant, $campagne)
    {
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiant);

        return SVClient::getInstance()->find(SVClient::getTypeByEtablissement($etablissement).'-'.$etablissement->identifiant.'-'.$campagne);
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

    public function createSV($identifiant, $campagne) {
        $sv = new SV();
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiant);

        $sv->identifiant = $etablissement->identifiant;
        $sv->type = SVClient::getTypeByEtablissement($etablissement);
        $sv->periode = $campagne;
        $sv->campagne = ''.$campagne.'-'.($campagne+1);
        $sv->constructId();
        $sv->storeDeclarant();
        $sv->storeStorage();

        return $sv;
    }

    public function createFromDR($identifiant, $campagne)
    {
        $sv = $this->createSV($identifiant, $campagne);

        $etablissement = $sv->getEtablissementObject();
        $cvi_acheteur = $etablissement->getCvi();
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
                $svCepage = null;
                $hash = HashMapper::convert($cepage->getHash());
                if($cepage->getAppellation()->getKey() == "appellation_CREMANT" && $cepage->getKey() == "cepage_PN") {
                    $hash = HashMapper::convert($cepage->getCouleur()->getHash()).'/cepages/RS';
                } elseif($cepage->getAppellation()->getKey() == "appellation_CREMANT" && strpos($cepage->getKey(), "cepage_RB") === false) {
                    $hash = HashMapper::convert($cepage->getCouleur()->getHash()).'/cepages/BL';
                }
                foreach ($cepage->getProduitsDetails() as $detail) {
                    if(!$detail->getVolumeByAcheteur($cvi_acheteur, $drAcheteurType)) {
                        continue;
                    }

                    $denomination = $this->formatDenomination($detail->denomination);
                    if($detail->lieu) {
                        $denomination = strtoupper(trim(preg_replace('/[ ]+/', ' ', $detail->lieu)));
                    }

                    $detail = $sv->addProduit($dr->identifiant, $hash, $denomination);

                    if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/RS") !== false && $hasRebeches) {
                        $sv->addProduit($dr->identifiant, str_replace("/cepages/RS", "/cepages/RBRS", $hash));
                    }

                    if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/BL") !== false && $hasRebeches) {
                        $sv->addProduit($dr->identifiant, str_replace("/cepages/BL", "/cepages/RBBL", $hash));
                    }

                    $svCepage = $detail->getCepage();
                }
                if($svCepage && count($svCepage->toArray(true, false)) == 1) {
                    $svCepage->getFirst()->superficie_recolte = $cepage->getTotalSuperficieVendusByCvi($drAcheteurType, $cvi_acheteur);
                }
                if($cepage->getVolumeAcheteur($cvi_acheteur, 'mouts')) {
                    $svProduit = $sv->addProduit($dr->identifiant, $hash);
                    $svProduit->add('volume_mouts', $cepage->getVolumeAcheteur($cvi_acheteur, 'mouts'));
                }
            }
        }

        return $sv;
    }

    protected function formatDenomination($denomination) {
        $denoms = array();

        if(preg_match('/VIEILLES[ ]*VIGNES/i', $denomination)) {
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

        return implode(" ", $denoms);
    }

    public function identifyProductCSV($line) {

        return CsvFileAcheteur::identifyProductCSV($line);
    }

    public function createFromCSV($identifiant, $campagne, CsvFileAcheteur $csv) {
        $sv = $this->createSV($identifiant, $campagne);

        foreach ($csv->getCsv() as $line) {
            if(!preg_match('/^[0-9]+/', $line[0])) {
                continue;
            }

            if (preg_match('/JEUNES +VIGNES/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            if (preg_match('/JUS DE RAISIN/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            $apporteur = EtablissementClient::getInstance()->findByCvi($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]);
            $produit = CsvFileAcheteur::identifyProductCSV($line);

            $produit = $sv->addProduit($apporteur->identifiant, $produit->getHash(), $line[CsvFileAcheteur::CSV_DENOMINATION]);

            if (strpos(KeyInflector::slugify($line[CsvFileAcheteur::CSV_APPELLATION]), 'MOUTS') !== false) {
                $produit->add('volume_mouts');
                $produit->add('volume_mouts_revendique');
                $produit->volume_mouts += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                $produit->volume_mouts_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT]);

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

    public function checkCSV(CsvFileAcheteur $csv, $identifiant, $campagne)
    {
        $check = [];
        $i = 0;

        foreach ($csv->getCsv() as $line) {
            $i++;

            if(!preg_match('/^[0-9]+/', $line[0])) {
                continue;
            }

            if ($line[CsvFileAcheteur::CSV_ACHETEUR_CVI] !== $identifiant) {
                $check[self::CSV_ERROR_ACHETEUR][] = [$i, $line[CsvFileAcheteur::CSV_ACHETEUR_CVI], "La ligne concerne un autre acheteur."];
                continue;
            }

            if (preg_match('/JEUNES +VIGNES/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            if (preg_match('/JUS DE RAISIN/i', $line[CsvFileAcheteur::CSV_APPELLATION])) {
                continue;
            }

            $apporteur = EtablissementClient::getInstance()->findByCvi($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]);

            if(! $apporteur) {
                $check[self::CSV_ERROR_APPORTEUR][] = [$i, $line[CsvFileAcheteur::CSV_RECOLTANT_CVI], 'Apporteur non reconnu : '.$line[CsvFileAcheteur::CSV_RECOLTANT_CVI]];
                continue;
            }

            $produit = CsvFileAcheteur::identifyProductCSV($line);

            if(! $produit) {
                $check[self::CSV_ERROR_PRODUIT][] = [$i, $line[CsvFileAcheteur::CSV_APPELLATION], "Produit non reconnu : ".$line[CsvFileAcheteur::CSV_APPELLATION]];
                continue;
            }

            if ($line[CsvFileAcheteur::CSV_VOLUME] <= 0) {
                $check[self::CSV_ERROR_VOLUME][] = [$i, $line[CsvFileAcheteur::CSV_VOLUME], "Le volume ne peut pas être nul"];
            }

            $volume = $line[CsvFileAcheteur::CSV_VOLUME] - $line[CsvFileAcheteur::CSV_VOLUME_DPLC];
            if (isset($line[CsvFileAcheteur::CSV_VOLUME_VCI])) {
                $volume -= $line[CsvFileAcheteur::CSV_VOLUME_VCI];
            }

            if ($volume <= 0)
            {
                $check[self::CSV_ERROR_VOLUME][] = [$i, $volume, "Le volume revendiqué ne peut pas être nul"];
            }
        }

        return $check;
    }

}
