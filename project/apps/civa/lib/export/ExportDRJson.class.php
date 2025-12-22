<?php

class ExportDRJson
{
    const ROOT_NODE = "declarationsRecolteProductionRecoltants";
    const APPORT_NODE = "declarationProduitsRecoltes";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public $PRODUITS_APPORTEUR_NODE = "apports";
    public $NUMERO_APPORTEUR = "numeroCVIApporteur";

    protected $dr;
    protected $raw;

    public function __construct(DR $declaration)
    {
        $this->dr = $declaration;
    }

    public function isValide()
    {
        return $this->dr->valide->statut === "VALIDE";
    }

    public function export()
    {
        $json = json_encode($this->raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_last_error_msg();
            return false;
        }

        return $json.PHP_EOL;
    }

    public function build()
    {
        $root = $this->getRootInfos();
        $root[self::APPORT_NODE]['produitsRecoltes'] = $this->getProduits();
        $root[self::SITE_NODE]['sites'] = $this->getSites();

        $this->raw = $root;
    }

    public function getRootInfos()
    {
        $infos = [
            'campagne' => $this->dr->campagne,
            'numeroCVIRecoltant' => $this->dr->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->dr->validee)
                                            ->format('d/m/Y 00:00:00'),
            self::APPORT_NODE => ['produitsRecoltes' => []],
            self::SITE_NODE => ['sites' => []]
        ];

        // Même codes que pour la SV : ER, RV, AU
        // N'existe pas dans le schéma de la DR
        if ($this->dr->exist('motif_modification') && $this->dr->motif_modification) {
            $motif = ['code' => $this->dr->motif_modification->motif];

            if ($motif['code'] === SV::SV_MOTIF_MODIFICATION_AUTRE) {
                $motif['libelleAutre'] = $this->dr->motif_modification->libelle;
            }

            $infos['motifModification'] = $motif;
        }

        return $infos;
    }

    public function getProduits()
    {
        $produits = [];

        foreach ($this->dr->getProduits() as $hash_produit => $produit) {
            if (strpos($hash_produit, '/cepage_RB') !== false) {
                continue; // pas les rebêches dans la boucle principale
            }

            $infoProduit = $this->buildInfoProduit($produit);

            if (! $infoProduit['recolteTotale']) {
                continue;
            }

            $produits[] = $infoProduit;
        }

        return $produits;
    }

    // Infos "manquantes" :
    // * volEauEliminee
    // * VSI
    // * volAlcoolAjoute
    // * volMoutJusDeRaisinsObtenu
    // * VolMcMcrObtenu <- pourquoi le V en majuscule ??
    // * volNonVinifie
    // * volEnVinification <- quelle diff avec recolteTotale ?
    // * conserveCaveParticuliereBailleurVini <- Y'a des bailleurs / matayer ?
    // * destinationVinifieeParBailleur
    // * conserveCaveParticuliereExploitant <- noeud cave particulière je suppose
    // * destinationVentesMouts <- à faire dans la boucle des mouts je suppose
    //
    // Infos "pas sur" :
    // * destinationVentesRaisins
    // * destinationApportsCaveCoop
    public function buildInfoProduit($produit)
    {
        // infos globales
        $infosProduit = [
            "typeRecoltant" => "EX", // Y a t'il des bailleurs vinificateurs ? (code BV)
            "zoneRecolte" => "B",
            "mentionValorisante" => $produit->denomination ?: "",
            "superficieRecolte" => number_format($produit->superficie / 100, 4, ".", ""),
            "recolteTotale" => number_format($produit->volume, 2, ".", ""),
            "volVinRevendicableOuCommercialisable" => number_format($produit->volume_revendique, 2, ".", "")
        ];

        if ($produit->exist('motif_non_recolte')) {
            $infosProduit['motifAbsenceRecolte'] = [
                'codeAbsenceRecolte' => in_array($produit->motif_non_recolte, ['PC', 'PS', 'IN', 'OG', 'AU']) ? $produit->motif_non_recolte : 'AU'
            ];

            if ($infosProduit['motifAbsenceRecolte']['codeAbsenceRecolte'] === 'AU') {
                $infosProduit['motifAbsenceRecolte']['motifAutreAbsenceRecolte'] = ''; // pas l'info j'ai l'impression
            }
        }

        if ($produit->vci) {
            $infosProduit['VCI'] = number_format($produit->vci, 2, ".", "");
        }

        if (count($produit->negoces)) {
            $ventesRaisins = [];
            foreach ($produit->negoces as $negoce) {
                $ventesRaisins[] = [
                    'numeroEvvDestinataire' => $negoce->cvi, // Quid de `destinataireTVA` ?
                    'volObtenuIssuRaisins' => $negoce->quantite_vendue
                ];
            }
            $infosProduit['destinationVentesRaisins'] = $ventesRaisins;
        }

        if (count($produit->cooperatives)) {
            $apportCaves = [];
            foreach ($produit->cooperatives as $coop) {
                $apportCaves[] = [
                    'numeroEvvCaveCoop' => $coop->cvi,
                    'volObtenuIssuRaisins' => $coop->quantite_vendue
                ];
            }
            $infosProduit['destinationApportsCaveCoop'] = $apportCaves;
        }

        if ($produit->dplc || $produit->lies) {
            $infosProduit['volDRAOuLiesSoutirees'] = number_format(
                ((float) $produit->dplc) + ((float) $produit->lies),
                2, ".", ""
            );
        }

        if (count($produit->mouts)) {
            $total_mouts = 0;
            foreach ($produit->mouts as $mout) {
                $total_mouts += $mout;
            }
            $infosProduit['volMoutApteAOP'] = format_number($total_mouts, 2, ".", "");
        }

        // rebêches
        if (strpos($produit->getHash(), '/CREMANT/') !== false && $produit->volume_revendique) {
            $produitsAssocies = ['typeAssociation' => 'REB'];
            $cepage = strrchr($this->dr->get($produit->getHash())->getCepage()->getHash(), '/');

            $hash_rebeche = str_replace($cepage, '/cepage_RB', $produit->getHash());
            $rebeches = $this->dr->get($hash_rebeche);
            $produitsAssocies['recolteTotaleProdAssocie'] = number_format($rebeches->volume_revendique, 2, ".", "");
            $produitsAssocies['codeProduitAssocie'] = $this->processCodeDouane($rebeches->getConfig()->getCodeDouane());

            $infosProduit['produitsAssocies'][] = $produitsAssocies;
        }

        return $infosProduit;
    }

    // Encore basé sur la SV
    protected function getSites()
    {
        $sites = [];
        foreach ($this->sv->stockage as $stockage) {
            $site = [];
            $site['codeSite'] = $stockage->numero;
            $sites[$stockage->numero] = $site;
        }

        foreach ($this->sv->getRecapProduits() as $hash => $produit) {
            if (strpos($hash, '/cepages/RB') !== false) {
                continue; // pas les rebêches dans les sites
            }

            $has_volume = $this->getApportRaisin($produit);

            if ($this->HAS_MOUTS) {
                $has_volume += $produit->volume_mouts;
            }

            if (! $has_volume) {
                continue;
            }

            $code_produit = $this->sv->getConfiguration()->get($produit->produit_hash)->code_douane;
            $mention = $produit->denominationComplementaire;

            foreach ($this->sv->stockage as $id => $lieu) {
                $produitsLieu = $lieu->produits;
                $produitsLieu = (is_array($produitsLieu) === false) ? $produitsLieu->toArray() : $produitsLieu;

                $volume = (array_key_exists($hash, $produitsLieu)) ? number_format($produitsLieu[$hash], 2, ".", "") : "0";

                $add = [
                    'codeProduit' => $this->processCodeDouane($code_produit),
                    'mentionValorisante' => $mention ?: "",
                    'volumeObtenu' => $volume
                ];

                $sites[$lieu->numero]['produits'][] = $add;
            }
        }

        return array_values($sites);
    }

    public function processCodeDouane($code_produit)
    {
        $code_produit = (strpos($code_produit, ',') === false) ? $code_produit : strstr($code_produit, ',', true);
        return $this->convertCodeDouane($code_produit);
    }

    public function convertCodeDouane($code_produit)
    {
        if ($code_produit === "1S001S 1") {
            return "1S001S";
        }

        if ($code_produit === "1R001S 1") {
            return "1R001S";
        }

        if ($code_produit === "1S001M00") {
            return "1S001M";
        }

        if ($code_produit === "1B001M00") {
            return "1B001M";
        }

        if ($code_produit === "1B070S") {
            return "1B070S09";
        }

        return str_replace(['D1', 'D2'], ['D6', 'D7'], $code_produit);
    }

    public function addHeaders($response)
    {
        $response->setHttpHeader('Content-Type', 'application/json');
        $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $this->dr->_id . '_douane.json"');
        $response->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $response->setHttpHeader('Pragma', '');
        $response->setHttpHeader('Cache-Control', 'public');
        $response->setHttpHeader('Expires', '0');
    }
}
