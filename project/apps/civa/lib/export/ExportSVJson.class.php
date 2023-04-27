<?php

class ExportSVJson
{
    protected $sv;
    protected $raw;

    public function __construct(SV $declaration)
    {
        $this->sv = $declaration;
    }

    public function isValide()
    {
        return $this->sv->valide->statut === "VALIDE";
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

    public function getProduits()
    {
        $produits = [];
        $apporteursParProduit = $this->sv->getApporteursParProduit();

        foreach ($apporteursParProduit as $hash_produit => $apporteurs_du_produit) {
            if (strpos($hash_produit, '/cepages/RB') !== false) {
                continue; // pas les rebêches dans la boucle principale
            }

            $apporteurs = [];

            foreach ($apporteurs_du_produit as $cvi) {
                $apporteur = $this->sv->apporteurs->get($cvi);
                $produit = $this->sv->get('/apporteurs/'.$cvi.'/'.$hash_produit);

                if (! $this->getApportRaisin($produit)) {
                    continue;
                }

                $apporteurs[] = $this->buildInfoApporteur($produit);

                if ($this->HAS_MOUTS && $produit->exist('volume_mouts')) {
                    $lastFournisseur = $apporteurs[array_key_last($apporteurs)];
                    unset($lastFournisseur['quantiteAchatRaisins']);
                    unset($lastFournisseur['volumeIssuRaisins']);
                    unset($lastFournisseur['produitsAssocies']);

                    $lastFournisseur['volumeAchatMouts'] = number_format($produit->volume_mouts, 2, ".", "");
                    $lastFournisseur['volumeIssuMouts'] = number_format($produit->volume_mouts_revendique, 2, ".", "");

                    $apporteurs[] = $lastFournisseur;
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

    public function buildInfoApporteur($produit)
    {
        // infos globales
        $infosApporteur = [
            $this->NUMERO_APPORTEUR => $produit->cvi,
            "zoneRecolte" => "B",
            "superficieRecolte" => number_format($produit->superficie_recolte, 2, ".", ""),
            $this->APPORT_RAISIN => $this->getApportRaisin($produit),
            "volumeIssuRaisins" => number_format($produit->volume_revendique, 2, ".", "")
        ];

        // volume à éliminer
        if ($this->HAS_VOLUME_A_ELIMINER && ($produit->vci || $produit->volume_detruit)) {
            $volumeAEliminer = [];

            if ($produit->volume_detruit) {
                $volumeAEliminer['volumeAEliminer'] = "".$produit->volume_detruit;
            }

            if ($produit->vci) {
                $volumeAEliminer['volumeComplementaireIndividuel'] = "".$produit->vci;
            }

            $infosApporteur['volumesAEliminer'] = $volumeAEliminer;
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

            if (! $this->getApportRaisin($produit)) {
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

        return str_replace(['D1', 'D2'], ['D6', 'D7'], $code_produit);
    }
}
