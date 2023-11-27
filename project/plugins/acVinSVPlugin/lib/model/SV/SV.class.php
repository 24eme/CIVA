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

    const SV_MOTIF_MODIFICATION_ERREUR = 'ER';
    const SV_MOTIF_MODIFICATION_REVENDICATION = 'RV';
    const SV_MOTIF_MODIFICATION_AUTRE = 'AU';

    protected $motifs_modification = [
        self::SV_MOTIF_MODIFICATION_ERREUR => "Erreur de saisie",
        self::SV_MOTIF_MODIFICATION_REVENDICATION => "Modification pour des besoins de revendication",
        self::SV_MOTIF_MODIFICATION_AUTRE => "Autre"
    ];

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

    public function addProduitsFromDR(DR $dr)
    {
        $cvi_acheteur = $this->identifiant;
        if (! $cvi_acheteur) {
            return;
        }

        $drAcheteurType = 'negoces';
        if($this->getType() == SVClient::TYPE_SV11) {
            $drAcheteurType = 'cooperatives';
        }

        // maj de l'apporteur
        if ($this->apporteurs->exist($dr->cvi)) {
            $this->apporteurs->remove($dr->cvi);
        }

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
                $denomination = SVClient::getInstance()->formatDenomination($detail->denomination);
                if($detail->lieu) {
                    $denomination = strtoupper(trim(preg_replace('/[ ]+/', ' ', $detail->lieu)));
                }

                $svDetail = $this->addProduit($dr->identifiant, $hash, $denomination);

                $svDetails[$svDetail->getHash()] = $svDetail;
                if(!isset($volumes[$svDetail->getHash()])) {
                    $volumes[$svDetail->getHash()] = 0;
                }
                $volumes[$svDetail->getHash()] += $volumeAcheteur;

                if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/RS") !== false && $hasRebeches) {
                    $this->addProduit($dr->identifiant, str_replace("/cepages/RS", "/cepages/RBRS", $hash));
                }

                if(strpos($hash, "/appellations/CREMANT/") !== false && strpos($hash, "/cepages/BL") !== false && $hasRebeches) {
                    $this->addProduit($dr->identifiant, str_replace("/cepages/BL", "/cepages/RBBL", $hash));
                }

                if($volumeAcheteur != $detail->volume) {
                    $svDetail->superficie_recolte = null;
                    continue;
                }

                if(!$detail->superficie) {
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

                if(!$svDetail->superficie_recolte) {
                    $svDetail->superficie_recolte = null;
                }
            }

            if($cepage->getVolumeAcheteur($cvi_acheteur, 'mouts')) {
                $svProduit = $this->addProduit($dr->identifiant, $hash);
                if (!$svProduit->exist('volume_mouts')) {
                    $svProduit->add('volume_mouts');
                    $svProduit->add('volume_mouts_revendique');
                    $svProduit->add('superficie_mouts');
                }
                $svProduit->volume_mouts += $cepage->getVolumeAcheteur($cvi_acheteur, 'mouts');
            }
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

    public function addApporteurHorsRegion($cvi, $raison_sociale, $pays)
    {
        if (array_key_exists($cvi, $this->apporteurs->toArray())) {
            return;
        }

        foreach ($this->listeProduitsHorsRegion() as $hash => $produit) {
            $p = $this->addProduit($cvi, $hash);
            $p->nom = $raison_sociale;
            $p->commune = $pays;
        }
    }

    public function listeProduitsHorsRegion()
    {
        return array_filter(ConfigurationClient::getInstance()->getCurrent()->declaration->getProduitsAll(), function ($produit) {
            if ($produit->getAppellation()->getCode() !== 'VINTABLE') {
                return false;
            }

            if (in_array($produit->getCepage()->getCode(), ['BL', 'RG', 'RS']) === false) {
                return false;
            }

            return true;
        });
    }

    public function getApporteursParProduit()
    {
        $produits = [];

        foreach ($this->apporteurs as $apporteur) {
            foreach ($apporteur->getProduits() as $produit) {
                $produits[substr($produit->getHash(), 23)][] = $apporteur->getCvi();
            }
        }

        return $produits;
    }

    public function getRecapProduits() {
        $recap = array();
        foreach($this->getProduits() as $produit) {
            $key = $produit->getProduitHash().'/'.$produit->getKey();
            if(!isset($recap[$key])) {
                $recap[$key] = new stdClass();
                $recap[$key]->produit_hash = $produit->getProduitHash();
                $recap[$key]->libelle = $produit->getLibelle();
                $recap[$key]->denominationComplementaire = $produit->denomination_complementaire;
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
        if(!$etablissement && preg_match("/^[0-9]{10}|[A-Z]{2}[A-Z0-9]{8,12}$/", $identifiant)) {
            $etablissement = new stdClass();
            $etablissement->cvi = $identifiant;
            $etablissement->nom = $identifiant;
        }

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

    public function getVolumeCremantApporteur($identifiant, $cepage)
    {
        return array_reduce($this->apporteurs->get($identifiant)->toArray(), function ($total, $produit) use ($cepage) {
            // si pas cremant
            if (strpos($produit->getHash(), '/CREMANT/') === false) {
                return $total;
            }

            // si rebeche
            if (strpos($produit->getHash(), '/cepages/RB') !== false) {
                return $total;
            }

            // si addition crémant rosé, et que le produit n'est pas du rosé
            if (in_array($cepage, ['/RS', '/PN'])
                && strpos($produit->getHash(), '/cepages/RS') === false && strpos($produit->getHash(), '/cepages/PN') === false) {
                return $total;
            }

            // si addition blanc, et que le produit est du rosé
            if (in_array($cepage, ['/RS', '/PN']) === false
                && (strpos($produit->getHash(), '/cepages/RS') !== false || strpos($produit->getHash(), '/cepages/PN') !== false)) {
                return $total;
            }

            // defaut + denom
            foreach ($produit as $denom) {
                $total += $denom->volume_revendique;
            }

            return $total;
        }, 0);
    }

    public function getVolumeCremantTotal()
    {
        return array_reduce($this->getRecapProduits(), function ($total, $produit) {
            // si pas cremant
            if (strpos($produit->produit_hash, '/CREMANT/') === false) {
                return $total;
            }

            // si rebeche
            if (strpos($produit->produit_hash, '/cepages/RB') !== false) {
                return $total;
            }

            return $total += $produit->volume_revendique;
        }, 0);
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

    public function setMotifModification($type, $autre = null)
    {
        $this->add('motif_modification')->motif = $type;

        if ($type === self::SV_MOTIF_MODIFICATION_AUTRE) {
            if ($autre == null) {
                throw new sfException('Le motif ne peut être vide quand Autre est sélectionné');
            }

            $this->add('motif_modification')->libelle = $autre;
        }
    }

    public function getMotifsModification()
    {
        return $this->motifs_modification;
    }

    public function validate()
    {
        if ($this->valide->date_saisie === null) {
            $this->valide->date_saisie = (new DateTimeImmutable())->format('Y-m-d');
        }
        $this->valide->date_modification = (new DateTimeImmutable())->format('Y-m-d');
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

    public function getValidee() {

        return $this->isValide();
    }

    public function getModifiee()
    {
        return $this->isValide() === false && $this->valide->date_saisie;
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
            $volume = $produit->volume_revendique;

            if ($this->getType() === SVClient::TYPE_SV12) {
                $volume += $produit->volume_mouts_revendique;
            }

            $lignes .= $drmGenerateCSV->createRowMouvementProduitDetail($produit->produit_hash, "entrees", "recolte", $volume, null, $produit->denominationComplementaire);
        }

        $lignes .= $drmGenerateCSV->createRowMouvementProduitDetail("Lies et bourbes", "entrees", "recolte", $this->lies);
        $lignes .= $drmGenerateCSV->createRowMouvementProduitDetail("Rebêches", "entrees", "recolte", $this->rebeches);

        return $lignes;
    }

    public function getFamilleCalculee() {
        if($this->getType() == 'TYPE_SV11') {

            return "COOPERATIVE";
        }

        return "NEGOCIANT";
    }

    public function recalculeVolumesRevendiques() {
        foreach($this->getProduits() as $produit) {
            $produit->volume_revendique = null;
            if(!$produit->getTauxExtraction()) {
                continue;
            }
            $produit->volume_revendique = round($produit->quantite_recolte / $produit->getTauxExtraction(), 2);
        }
    }

    public function hasVolumeRevendique() {
        foreach($this->getProduits() as $produit) {
            if($produit->volume_revendique) {
                return true;
            }
        }

        return false;
    }

    public function isFromCSV() {

        return $this->exist('_attachments') && count($this->_attachments);
    }
}
