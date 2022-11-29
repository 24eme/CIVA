<?php

class SVClient extends acCouchdbClient {

    const TYPE_SV11 = "SV11";
    const TYPE_SV12 = "SV12";

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

    protected function createDR($identifiant, $campagne) {
        $sv = new SV();
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiant);

        $sv->identifiant = $etablissement->identifiant;
        $sv->type = SVClient::getTypeByEtablissement($etablissement);
        $sv->periode = '2021';
        $sv->campagne = '2021-2022';
        $sv->constructId();
        $sv->storeDeclarant();

        return $sv;
    }

    public function createFromDR($identifiant, $campagne)
    {
        $sv = $this->createDR($identifiant, $campagne);

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

                    $detail = $sv->addProduit($dr->identifiant, $hash, trim(str_replace($etablissement->nom, null, $detail->denomination)));

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
            }
        }

        return $sv;
    }

    public function identifyProductCSV($line) {

        return CsvFileAcheteur::identifyProductCSV($line);
    }

    public function createFromCSV($identifiant, $campagne, $csvFile) {
        $sv = $this->createDR($identifiant, $campagne);

        foreach (explode("\n", $csvFile) as $lineContent) {
            $line = str_getcsv($lineContent, ";");

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

            if(!$apporteur) {
                throw new Exception("Apporteur non trouvé");
            }

            $produit = CsvFileAcheteur::identifyProductCSV($line);
            $prod = array();

            if(!$produit) {
                throw new Exception("Produit non trouvé : ".implode(";", $line));
            }

            $produit = $sv->addProduit($apporteur->identifiant, $produit->getHash(), $line[CsvFileAcheteur::CSV_DENOMINATION]);

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

        return $sv;
    }

    protected function recodeNumber($value) {

        return round(str_replace(",", ".", $value)*1, 2);
    }

}
