<?php
/**
 * Model for SV
 *
 */

class SV extends BaseSV
{
    use HasDeclarantDocument;

    const DEFAULT_KEY = 'DEFAUT';

    public function getConfiguration() {
        return ConfigurationClient::getInstance()->getCurrent();
    }

    public function constructId() {
        $id = $this->type.'-' . $this->identifiant . '-' . $this->periode;
        $this->set('_id', $id);
    }

    public function __construct()
    {
        parent::__construct();
        $this->initDocuments();
    }

    public function initDocuments()
    {
        $this->initDeclarantDocument();
    }

    public function getEtablissement() {
         $etablissement = EtablissementClient::getInstance()->find($this->identifiant);

         return $etablissement;
    }

    public function getProduits($region = null) {
        $produits = array();

        foreach($this->apporteurs as $apporteur) {
            $produits = array_merge($produits, $apporteur->getProduits());
        }

        return $produits;
    }

    public function getRecapProduits() {
        $recap = array();
        foreach($this->getProduits() as $produit) {
            if(!isset($recap[$produit->getProduitHash()])) {
                $recap[$produit->getProduitHash()] = new stdClass();
                $recap[$produit->getProduitHash()]->libelle = $produit->libelle;
                $recap[$produit->getProduitHash()]->libelle_html = $produit->getLibelleHtml();
                $recap[$produit->getProduitHash()]->superficie_recolte = 0;

                if ($this->getType() === SVClient::TYPE_SV11) {
                    $recap[$produit->getProduitHash()]->volume_recolte = 0;
                    $recap[$produit->getProduitHash()]->usages_industriels = 0;
                    $recap[$produit->getProduitHash()]->vci = 0;
                }

                if ($this->getType() === SVClient::TYPE_SV12) {
                    $recap[$produit->getProduitHash()]->quantite_recolte = 0;
                }

                $recap[$produit->getProduitHash()]->volume_revendique = 0;
                $recap[$produit->getProduitHash()]->apporteurs = array();
            }

            $recapProduit = $recap[$produit->getProduitHash()];
            $recapProduit->superficie_recolte += $produit->superficie_recolte;

            if ($this->getType() === SVClient::TYPE_SV11) {
                $recapProduit->volume_recolte += $produit->volume_recolte;
                $recapProduit->usages_industriels += (isset($produit->usages_industriels))
                    ?  $produit->usages_industriels
                    : 0;
                $recapProduit->vci += (isset($produit->vci))
                    ? $produit->vci
                    : 0;

            }

            if ($this->getType() === SVClient::TYPE_SV12) {
                $recapProduit->quantite_recolte += $produit->quantite_recolte;
            }

            $recapProduit->volume_revendique += $produit->volume_revendique;
            $recapProduit->apporteurs[$produit->identifiant] = $produit->nom;
        }

        return $recap;
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

        $hashToAdd = str_replace("/declaration/", '', $hash);
        $exist = $this->exist('apporteurs/'.$identifiant.'/'.$hashToAdd);
        $produit = $this->apporteurs->add($identifiant)->add($hashToAdd)->add($detailKey);
        $produit->denomination_complementaire = null;
        if($denominationComplementaire) {
            $produit->denomination_complementaire = $denominationComplementaire;
        }
        $produit->getLibelle();
        $produit->cvi = $etablissement->cvi;
        $produit->nom = $etablissement->nom;
        $produit->commune = $etablissement->declaration_commune;
        $produit->identifiant = $etablissement->identifiant;
        if(!$exist) {
            //$this->declaration->reorderByConf();
        }
        return $this->get($produit->getHash());
    }
}
