<?php

class ExportDRJson
{
    const ROOT_NODE = "declarationsRecolteProductionRecoltants";
    const APPORT_NODE = "declarationProduitsRecoltes";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public $HAS_MOUTS = false;
    public $HAS_VOLUME_A_ELIMINER = true;

    public $PRODUITS_APPORTEUR_NODE = "apports";
    public $NUMERO_APPORTEUR = "numeroCVIApporteur";
    public $APPORT_RAISIN = "volumeApportRaisins";

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

    public function getApportRaisin($produit)
    {
        return number_format($produit->volume_recolte, 2, ".", "");
    }

    public function getProduits()
    {
        $produits = [];

        foreach ($this->dr->getProduits() as $hash_produit => $produit) {
            if (strpos($hash_produit, '/cepages/RB') !== false) {
                continue; // pas les rebêches dans la boucle principale
            }

            $infoProduit = $this->buildInfoProduit($produit);

            if (! $infoProduit) {
                continue;
            }

            $produits[] = $infoProduit;

            $produits[] = [
                "typeRecoltant" => "EX", // Y a t'il des bailleurs vinificateurs ? (code BV)
                "codeProduit" => $this->processCodeDouane($produit->getConfig()->getCodeDouane()),
                "mentionValorisante" => $produit->denomination ?: "",
                "zoneRecolte" => "B",
                $this->PRODUITS_APPORTEUR_NODE => $apporteurs
            ];

            $apporteurs = [];

            foreach ($apporteurs_du_produit as $cvi) {
                $apporteur = $this->sv->apporteurs->get($cvi);
                $produit = $this->sv->get('/apporteurs/'.$cvi.'/'.$hash_produit);

                $apporteur = $this->buildInfoRecoltant($produit);

                if ($this->getApportRaisin($produit)) {
                    $apporteurs[] = $apporteur;
                }

                if ($this->HAS_MOUTS && $produit->exist('volume_mouts')) {
                    unset($apporteur['quantiteAchatRaisins']);
                    unset($apporteur['volumeIssuRaisins']);
                    unset($apporteur['produitsAssocies']);

                    $apporteur['volumeAchatMouts'] = number_format($produit->volume_mouts, 2, ".", "");
                    $apporteur['volumeIssuMouts'] = number_format($produit->volume_mouts_revendique, 2, ".", "");
                    $apporteur['superficieRecolte'] = number_format($produit->superficie_mouts / 100, 4, ".", "");

                    $apporteurs[] = $apporteur;
                }
            }

            if (empty($apporteurs)) {
                continue;
            }

            $produits[] = [
                "codeProduit" => $this->processCodeDouane($produit->getConfig()->getCodeDouane()),
                "mentionValorisante" => $produit->denomination_complementaire ?: "",
                $this->PRODUITS_APPORTEUR_NODE => $apporteurs
            ];
        }

        return $produits;
    }

    public function buildInfoProduit($produit)
    {
        // infos globales
        $infosProduit = [
            "typeRecoltant" => "EX", // Y a t'il des bailleurs vinificateurs ? (code BV)
            "zoneRecolte" => "B",
            "mentionValorisante" => $produit->denomination ?: "",
            "superficieRecolte" => number_format($produit->superficie / 100, 4, ".", ""),
            $this->APPORT_RAISIN => $this->getApportRaisin($produit),
            "recolteTotale" => number_format($produit->volume, 2, ".", "")
        ];

        // volume à éliminer
        if ($this->HAS_VOLUME_A_ELIMINER && ($produit->vci || $produit->volume_detruit)) {
            $volumeAEliminer = [];

            if ($produit->volume_detruit) {
                $volumeAEliminer['volumeAEliminer'] = number_format($produit->volume_detruit, 2, ".", "");
            }

            if ($produit->vci) {
                $volumeAEliminer['volumeComplementaireIndividuel'] = number_format($produit->vci, 2, ".", "");
            }

            if (empty($volumeAEliminer) === false) {
                $infosApporteur['volumesAEliminer'] = $volumeAEliminer;
            }
        }

        // rebêches
        if (strpos($produit->getHash(), '/CREMANT/') !== false && $produit->volume_revendique) {
            $produitsAssocies = ['typeAssociation' => 'REB'];
            $cepage = strrchr($this->sv->get($produit->getHash())->getCepage()->getHash(), '/');

            if (in_array($cepage, ['/RS', '/PN'])) {
                //si crémant rosé, on cherche les rebeches rosées
                $hash_rebeche = str_replace($cepage, '/RBRS', $produit->getHash());
            } else {
                $hash_rebeche = str_replace($cepage, '/RBBL', $produit->getHash());
            }

            if (($denom = strrchr($hash_rebeche, '/')) !== '/DEFAUT') {
                if ($this->sv->exist($hash_rebeche) === false) {
                    $hash_rebeche = str_replace($denom, '/DEFAUT', $hash_rebeche);
                }
            }

            if ($this->sv->hasRebechesInProduits()) {
                // rebeches en détail
                if ($this->sv->exist($hash_rebeche) === false) {
                    $hash_rebeche = str_replace(['/RBBL', '/RBRS'], '/RB', $hash_rebeche);

                    // cas Crémant rosé, mais rebeche blanc ????
                    if ($this->sv->exist($hash_rebeche) === false) {
                        $hash_rebeche = str_replace('/RB', '/RBBL', $hash_rebeche);
                    }
                }

                $rebeches = $this->sv->get($hash_rebeche);
                $produitsAssocies['codeProduitAssocie'] = $this->processCodeDouane($rebeches->getConfig()->getCodeDouane());

                $total_cremant_operateur = $this->sv->getVolumeCremantApporteur($produit->cvi, $cepage);
                $pourcentage_cremant_operateur = ($produit->volume_revendique * 100) / $total_cremant_operateur;

                $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = number_format(($pourcentage_cremant_operateur * $rebeches->volume_revendique) / 100, 2, ".", "");
            } else {
                // % des rebeches totales
                $total_cremant = $this->sv->getVolumeCremantTotal();
                $pourcentage_cremant = ($produit->volume_revendique * 100) / $total_cremant;

                $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = number_format(($pourcentage_cremant * $this->sv->getRebeches()) / 100, 2, ".", "");
                $produitsAssocies['codeProduitAssocie'] = (in_array($cepage, ['/RS', '/PN'])) ? "4S999B" : "4B999B";
            }

            $infosApporteur['produitsAssocies'][] = $produitsAssocies;
        }

        return $infosApporteur;
    }

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
