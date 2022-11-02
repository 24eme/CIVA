<?php

class SVClient extends acCouchdbClient {

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

    public function createFromDR($identifiant, $campagne)
    {
        $sv = new SV();
        $etablissement = EtablissementClient::getInstance()->find('ETABLISSEMENT-'.$identifiant);
        $sv->identifiant = $etablissement->identifiant;
        $sv->type = SVClient::getTypeByEtablissement($etablissement);
        $sv->periode = '2021';
        $sv->campagne = '2021-2022';
        $sv->constructId();
        $sv->storeDeclarant();
        $cvi_acheteur = $etablissement->getCvi();
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
            if($dr->recolte->getTotalDontVciVendusByCvi('negoces', $cvi_acheteur) || $dr->recolte->getTotalDontVciVendusByCvi('cooperatives', $cvi_acheteur)) {
                //$this->addAppellation("declaration/certification/genreVCI");
            }
        }

        return $sv;
    }
}
