<?php
/**
 * Model for SV
 *
 */

class SV extends BaseSV {

    const DEFAULT_KEY = 'DEFAUT';

    public function getConfiguration() {
        return ConfigurationClient::getInstance()->getCurrent();
    }

    public static function buildDetailKey($denominationComplementaire = null, $hidden_denom = null) {
        $detailKey = self::DEFAULT_KEY;

        if($denominationComplementaire || $hidden_denom){
            $detailKey = substr(hash("sha1", KeyInflector::slugify(trim($denominationComplementaire).trim($hidden_denom))), 0, 7);
        }

        return $detailKey;
    }

    public function addProduit($identifiant, $hash, $denominationComplementaire = null, $hidden_denom = null) {
        $etablissement = EtablissementClient::getInstance()->findByIdentifiant($identifiant, acCouchdbClient::HYDRATE_JSON);
        $detailKey = self::buildDetailKey($denominationComplementaire, $hidden_denom);

        $hashToAdd = preg_replace("|/declaration/|", '', $hash);
        $exist = $this->exist('apporteurs/'.$identifiant.'/'.$hashToAdd);
        $produit = $this->apporteurs->add($identifiant)->add($hashToAdd)->add($detailKey);
        $produit->denomination_complementaire = null;
        if($denominationComplementaire) {
            $produit->denomination_complementaire = $denominationComplementaire;
        }
        $produit->getLibelle();
        $produit->cvi = $etablissement->cvi;
        $produit->nom = $etablissement->nom;
        $produit->identifiant = $etablissement->identifiant;
        if(!$exist) {
            //$this->declaration->reorderByConf();
        }
        return $this->get($produit->getHash());
    }
}