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

    protected $produits_rebeches = null;

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

    public function storeStorage() {
        $etablissement = $this->getEtablissementObject();
        $lieux = $etablissement->getLieuxStockage(false, $this->identifiant);
        foreach($lieux as $lieu) {
            $this->stockage->add($lieu->numero, $lieu);
        }
    }

    public function getDeclarantObject() {

        return $this->getEtablissement();
    }

    public function getEtablissement() {
         $etablissement = EtablissementClient::getInstance()->find($this->id_etablissement);

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
                    $recap[$key]->volume_detruit = 0;
                    $recap[$key]->vci = 0;
                }

                if ($this->getType() === SVClient::TYPE_SV12) {
                    $recap[$key]->quantite_recolte = 0;
                    $recap[$key]->volume_mouts = 0;
                    $recap[$key]->volume_mouts_revendique = 0;
                    $recap[$key]->superficie_mouts = 0;
                }

                $recap[$key]->volume_revendique = 0;
                $recap[$key]->apporteurs = array();
            }

            $recapProduit = $recap[$key];
            $recapProduit->superficie_recolte += $produit->superficie_recolte;

            if ($this->getType() === SVClient::TYPE_SV11) {
                $recapProduit->volume_recolte += $produit->volume_recolte;
                $recapProduit->volume_detruit += $produit->volume_detruit;
                $recapProduit->vci += $produit->vci;

            }

            if ($this->getType() === SVClient::TYPE_SV12) {
                $recapProduit->quantite_recolte += $produit->quantite_recolte;
                $recapProduit->volume_mouts += ($produit->exist('volume_mouts')) ? $produit->volume_mouts : 0;
                $recapProduit->volume_mouts_revendique += ($produit->exist('volume_mouts_revendique')) ? $produit->volume_mouts_revendique : 0;
                $recapProduit->superficie_mouts += ($produit->exist('superficie_mouts')) ? $produit->superficie_mouts : 0;
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
        $exist = $this->exist('apporteurs/'.$etablissement->cvi.'/'.$hashToAdd);
        $produit = $this->apporteurs->add($etablissement->cvi)->add($hashToAdd)->add($detailKey);
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
            $this->apporteurs->get($etablissement->cvi)->reorderByConf();
        }
        return $this->get($produit->getHash());
    }

    public function hasRebechesInProduits()
    {
        if ($this->produits_rebeches !== null) {
            return empty($this->produits_rebeches) === false;
        }

        $this->produits_rebeches = array_filter($this->getDocument()->getRecapProduits(), function ($v, $k) { return strpos($k, '/cepages/RB') !== false; }, ARRAY_FILTER_USE_BOTH);
        return empty($this->produits_rebeches) === false;
    }

    public function hasCremantInProduits()
    {
        return count(array_filter($this->getDocument()->getRecapProduits(), function ($v, $k) { return strpos($k, '/CREMANT/') !== false && strpos($k, '/cepages/RB') === false; }, ARRAY_FILTER_USE_BOTH)) > 0;
    }

    public function hasVolumeCremantInProduits()
    {
        return count(array_filter($this->getRecapProduits(), function ($v, $k) { return strpos($k, '/CREMANT/') !== false && strpos($k, '/cepages/RB') === false && $v->volume_revendique; }, ARRAY_FILTER_USE_BOTH)) > 0;
    }

    public function calculateRebeches()
    {
        $total_rebeches = $this->getDocument()->rebeches ?? null;

        if ($this->hasRebechesInProduits()) {
            $total_rebeches = array_reduce($this->produits_rebeches, function ($total, $p) { return $total += $p->volume_revendique; }, 0);
        }

        return $total_rebeches;
    }

    public function getNotEmptyLieuxStockage()
    {
        $lieux = [];

        foreach ($this->stockage as $stockage) {
            if ($stockage->isPrincipale() || ($stockage->exist('produits') && empty($stockage->produits->toArray()) === false)) {
                $lieux[] = $stockage;
            }
        }

        return $lieux;
    }

    public function validate()
    {
        $this->valide->date_saisie = (new DateTimeImmutable())->format('Y-m-d');
        $this->valide->statut = self::STATUT_VALIDE;

        $this->getRebeches();

        if($this->lies === null) {
            $this->lies = 0;
        }

        if($this->rebeches === null) {
            $this->rebeches = 0;
        }
    }

    public function isValide()
    {
        return $this->valide->statut === self::STATUT_VALIDE;
    }

    public function devalidate()
    {
        $this->valide->statut = null;
    }

    public function getRebeches() {
        if($this->hasRebechesInProduits()) {
            $this->rebeches = $this->calculateRebeches();
        }

        return $this->_get('rebeches');
    }

    public function getDRMEdiMouvementRows(DRMGenerateCSV $drmGenerateCSV)
    {
        $lignes = "";

        foreach ($this->getRecapProduits() as $produit) {
            $lignes .= $drmGenerateCSV->createRowMouvementProduitDetail($produit->libelle, "entrees", "recolte", $produit->volume_revendique);
        }

        return $lignes;
    }
}
