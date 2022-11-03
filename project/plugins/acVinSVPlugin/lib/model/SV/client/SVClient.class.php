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

        $cvi_acheteur = $sv->getEtablissementObject()->getCvi();
        if(!$cvi_acheteur) {
            return;
        }
        $drs = DRClient::getInstance()->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach ($drs as $id => $doc) {
            $dr = DRClient::getInstance()->find($id);
            foreach ($dr->getProduitsDetails() as $detail) {
                if($detail->getVolumeByAcheteur($cvi_acheteur)) {
                    $sv->addProduit($dr->identifiant, HashMapper::convert($detail->getCepage()->getHash()));
                }

                if($detail->getVolumeByAcheteur($cvi_acheteur, 'cooperatives')) {
                    $sv->addProduit($dr->identifiant, HashMapper::convert($detail->getCepage()->getHash()));
                }
            }
        }

        return $sv;
    }

    public function identifyProductCSV($line) {
        $appellation = $line[CsvFileAcheteur::CSV_APPELLATION];
        $appellation = preg_replace("/^0$/", "", $appellation);
        $appellation = preg_replace("/AOC ALSACE PINOT NOIR ROUGE/i", "AOC Alsace PN rouge", $appellation);

        $lieu = $line[CsvFileAcheteur::CSV_LIEU];
        $lieu = preg_replace("/^0$/", "", $lieu);

        $cepage = $line[CsvFileAcheteur::CSV_CEPAGE];
        $cepage = preg_replace("/^0$/", "", $cepage);
        $cepage = preg_replace("/Gewurzt\./i", "Gewurztraminer", $cepage);
        $cepage = preg_replace("/Muscat d'Alsace/i", "Muscat", $cepage);
        $cepage = preg_replace("/^Klevener/i", "Klevener de Heiligenstein ", $cepage);

        if(preg_match("/(AOC ALSACE PINOT NOIR|AOC ALSACE PN ROUGE)/i", $appellation)) {
            $cepage = null;
        }

        $vtsgn = $line[CsvFileAcheteur::CSV_VTSGN];
        $vtsgn = preg_replace("/^0$/", "", $vtsgn);

        $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s %s", $appellation, $lieu, $cepage, $vtsgn)));

        if(!$produit) {
            $produit = ConfigurationClient::getConfiguration()->identifyProductByLibelle(trim(sprintf("%s %s %s", $appellation, $cepage, $vtsgn)));
        }

        return $produit;
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
                throw new Exception("Produit non trouvé");
            }

            $produit = $sv->addProduit($apporteur->identifiant, $produit->getHash());

            $produit->superficie_recolte += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);

            if($sv->getType() == SVClient::TYPE_SV12) {
                $produit->quantite_recolte += (int) $line[CsvFileAcheteur::CSV_QUANTITE];
                $produit->volume_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_VOLUME_REVENDIQUE]);
            }
            if($sv->getType() == SVClient::TYPE_SV11) {
                $produit->volume_recolte += (int) $line[CsvFileAcheteur::CSV_VOLUME];
                $produit->volume_detruit += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_VOLUME_DPLC + 1]);
                $produit->volume_vci += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_VOLUME_VCI + 1]);
                $produit->volume_revendique += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_VOLUME_REVENDIQUE]);
            }
        }

        return $sv;
    }

    protected function recodeNumber($value) {

        return round(str_replace(",", ".", $value)*1, 2);
    }

}
