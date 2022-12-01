<?php
/**
 * Model for SV
 *
 */

class SV extends BaseSV
{
    use HasDeclarantDocument;

    const DEFAULT_KEY = 'DEFAUT';
    const STATUT_VALIDE = 'VALIDE';

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
            $key = $produit->getProduitHash().'/'.$produit->getKey();
            if(!isset($recap[$key])) {
                $recap[$key] = new stdClass();
                $recap[$key]->libelle = $produit->getLibelle();
                $recap[$key]->libelle_html = $produit->getLibelleHtml();
                $recap[$key]->superficie_recolte = 0;

                if ($this->getType() === SVClient::TYPE_SV11) {
                    $recap[$key]->volume_recolte = 0;
                    $recap[$key]->usages_industriels = 0;
                    $recap[$key]->vci = 0;
                }

                if ($this->getType() === SVClient::TYPE_SV12) {
                    $recap[$key]->quantite_recolte = 0;
                    $recap[$key]->volume_mouts = 0;
                }

                $recap[$key]->volume_revendique = 0;
                $recap[$key]->apporteurs = array();
            }

            $recapProduit = $recap[$key];
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
                $recapProduit->volume_mouts += ($produit->exist('volume_mouts')) ? $produit->volume_mouts : 0;
            }

            $recapProduit->volume_revendique += $produit->volume_revendique;
            $recapProduit->apporteurs[$produit->identifiant] = $produit->nom;
            $recapProduit->taux_extraction = $produit->getTauxExtractionDefault();
        }

        $recapSorted = array();

        foreach($this->getDocument()->getConfiguration()->getProduits() as $hashProduit => $child) {
            foreach(array_keys($recap) as $hash) {
                if(strpos($hash, $hashProduit) === false) {
                    continue;
                }
                $recapSorted[$hash] = $recap[$hash];
                unset($recap[$hash]);
            }
        }

        return $recapSorted;
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
            $this->apporteurs->get($identifiant)->reorderByConf();
        }
        return $this->get($produit->getHash());
    }

    public function validate()
    {
        $this->valide->date_saisie = (new DateTimeImmutable())->format('Y-m-d');
        $this->valide->statut = self::STATUT_VALIDE;
    }

    public function isValide()
    {
        return $this->valide->statut === self::STATUT_VALIDE;
    }
}
