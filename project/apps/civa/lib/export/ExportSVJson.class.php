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

            // pour le code_douane
            $produitFromConf = $this->sv->getConfiguration()->get($hash_produit);

            foreach ($apporteurs_du_produit as $cvi) {
                $apporteur = $this->sv->apporteurs->get($cvi);
                $produit = $apporteur->get(str_replace('/declaration/', '', $hash_produit))->getFirst();

                $apporteurs[] = $this->buildInfoApporteur($produit, $hash_produit);

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

            $produits[] = [
                "codeProduit" => strstr($produitFromConf->code_douane, ",", true),
                "mentionValorisante" => $produit->denomination_complementaire ?: "",
                $this->PRODUITS_APPORTEUR_NODE => $apporteurs
            ];
        }

        return $produits;
    }

    public function buildInfoApporteur($produit, $hash_produit)
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
        if (strpos($hash_produit, '/CREMANT/') !== false && $produit->volume_revendique) {
            $produitsAssocies = ['typeAssociation' => 'REB'];
            $cepage = strrchr($hash_produit, '/');

            if (in_array($cepage, ['/RS', '/PN'])) {
                //si crémant rosé, on cherche les rebeches rosées
                $hash_rebeche = str_replace($cepage, '/RBRS', $hash_produit);
            } else {
                $hash_rebeche = str_replace($cepage, '/RBBL', $hash_produit);
            }

            $produitsAssocies['codeProduitAssocie'] = strstr($this->sv->getConfiguration()->get($hash_rebeche)->code_douane, ',', true);

            if ($this->sv->hasRebechesInProduits()) {
                // rebeches en détail
                $apporteur = $this->sv->apporteurs->get($produit->cvi);
                $rebeches = $apporteur->get(str_replace('/declaration/', '', $hash_rebeche))->getFirst();

                $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = number_format($rebeches->volume_revendique, 2, ".", "");
            } else {
                // % des rebeches totales
                $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = number_format($produit->volume_revendique * 100 / $this->sv->rebeches, 2, ".", "");
            }

            $infosApporteur['produitsAssocies'][] = $produitsAssocies;
        }

        return $infosApporteur;
    }

    protected function getSites()
    {
        $sites = [];
        foreach ($this->sv->getNotEmptyLieuxStockage() as $stockage) {
            $site = [];
            $site['codeSite'] = $stockage->numero;
            $sites[$stockage->numero] = $site;
        }

        foreach ($this->sv->getRecapProduits() as $hash => $produit) {
            $code_produit = $this->sv->getConfiguration()->get($produit->produit_hash)->code_douane;
            $mention = $produit->denominationComplementaire;

            foreach ($this->sv->getNotEmptyLieuxStockage() as $id => $lieu) {
                $produitsLieu = $lieu->produits;
                $produitsLieu = (is_array($produitsLieu) === false) ? $produitsLieu->toArray() : $produitsLieu;
                if (array_key_exists($hash, $produitsLieu)) {
                    $add = [
                        'codeProduit' => strstr($code_produit, ',', true),
                        'mentionValorisante' => $mention ?: "",
                        'volumeObtenu' => number_format($produitsLieu[$hash], 2, ".", "")
                    ];

                    $sites[$lieu->numero]['produits'][] = $add;
                }
            }
        }

        return array_values($sites);
    }
}
