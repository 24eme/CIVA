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
        if (!$etablissement) {
            return null;
        }

        $type = SVClient::getTypeByEtablissement($etablissement);
        $sv = SVClient::getInstance()->find(SVClient::getTypeByEtablissement($etablissement).'-'.$etablissement->cvi.'-'.$campagne);

        if(!$sv && $type == SVClient::TYPE_SV11) {
            $sv = SVClient::getInstance()->find(SVClient::TYPE_SV12.'-'.$etablissement->cvi.'-'.$campagne);
        }

        if(!$sv && $type == SVClient::TYPE_SV12) {
            $sv = SVClient::getInstance()->find(SVClient::TYPE_SV11.'-'.$etablissement->cvi.'-'.$campagne);
        }

        return $sv;
    }

    public function getAllIdsByCampagne($campagne)
    {
        $ids = $this->startkey('SV11-0000000000-0000')->endkey('SV12-9999999999-9999')->execute(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        $idsCampagne = [];
        foreach($ids as $id) {
            if (substr($id, strlen($id) - 4, 4) == $campagne) {
                $idsCampagne[] = $id;
            }
        }

        return $idsCampagne;
    }

    public function getAllByEtablissement($etablissement)
    {
        $type = self::getTypeByEtablissement($etablissement);
        $ids = $this->startkey(sprintf('%s-%s-%s', $type, $etablissement->cvi, '0000'))
                    ->endkey(sprintf('%s-%s-%s', $type, $etablissement->cvi, '9999'))
                    ->execute(acCouchdbClient::HYDRATE_ON_DEMAND)
                    ->getIds();

        return $ids;
    }

    public function getAll($campagne)
    {
        $ids = $this->getAllIdsByCampagne($campagne);

        return array_map(function ($id) {
            return $this->find($id);
        }, $ids);
    }

    public static function getTypeByEtablissement($etablissement) {
        $type = null;

        if($etablissement->exist('acheteur_raisin') && $etablissement->acheteur_raisin == "NegoCave") {
            $type = 'SV12';
        } elseif($etablissement->famille == EtablissementFamilles::FAMILLE_COOPERATIVE) {
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

        $drs = DRClient::getInstance()->findAllByCampagneAndCviAcheteur($campagne, $cvi_acheteur, acCouchdbClient::HYDRATE_ON_DEMAND);
        foreach ($drs as $id => $doc) {
            $dr = DRClient::getInstance()->find($id);
            if (! $dr) {
                throw new sfException('DR inconnue : '.$id);
            }

            $sv->addProduitsFromDR($dr);
        }

        return $sv;
    }

    public function formatDenomination($denomination) {
        $denoms = array();

        if(preg_match('/VI?E?I?LLES?[ ]*VIGNES?/i', $denomination)) {
            $denoms[] = 'VIEILLES VIGNES';
        }

        if(preg_match('/(-| |^)AB(-| |$)/i', $denomination)) {
            $denoms[] = 'BIO';
        }

        if(preg_match('/(-| |^)BIO(-| |$)/i', $denomination)) {
            $denoms[] = 'BIO';
        }

        if(preg_match('/(-| |^)BIO-?LOGI(E|C|QUE)(-| |$)/i', $denomination)) {
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

            $denomination = $line[CsvFileAcheteur::CSV_DENOMINATION];
            if ($produit->hasLieuEditable()) {
                $denomination = $line[CsvFileAcheteur::CSV_LIEU];
            }

            $produit = $sv->addProduit($apporteur->identifiant ? $apporteur->identifiant : trim($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]), $produit->getHash(), $denomination);

            if (strpos(KeyInflector::slugify($line[CsvFileAcheteur::CSV_APPELLATION]), 'MOUTS') !== false) {
                $produit->add('volume_mouts');
                $produit->add('volume_mouts_revendique');
                $produit->add('superficie_mouts');
                $produit->volume_mouts += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_PRODUIT]);
                $produit->superficie_mouts += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SUPERFICIE]);

                // dans le cas où il n'y a que des moûts, on mets à 0 le volume revendiqué du produit
                // s'il y a un produit, on rajoute 0 donc ça change rien
                $produit->superficie_recolte += 0;
                $produit->quantite_recolte += 0;
                $produit->volume_recolte += 0;
                $produit->volume_revendique += 0;

                continue;
            }

            if (strpos($produit->getHash(), 'cepages/RB') !== false) {
                $produit->volume_recolte += CsvFileAcheteur::recodeNumber($line[CsvFileAcheteur::CSV_SV_VOLUME_VF]);
                $produit->volume_revendique = $produit->volume_recolte;
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

            $apporteur = EtablissementClient::getInstance()->findByCvi($line[CsvFileAcheteur::CSV_RECOLTANT_CVI]);

            if(! $apporteur && $produit->getAppellation()->existRendement() && strpos(strtoupper($line[CsvFileAcheteur::CSV_APPELLATION]), 'LIE') !== 0) {
                $check[self::CSV_ERROR_APPORTEUR][] = [$i];
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
            try {
                if(SVClient::getTypeByEtablissement($etablissement)) {

                    return $etablissement;
                }
            } catch(Exception $e) {

            }
        }

        return null;
    }

}
