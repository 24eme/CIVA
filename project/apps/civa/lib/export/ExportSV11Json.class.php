<?php

class ExportSV11Json extends ExportSVJson
{
    const ROOT_NODE = "declarationsProductionCaves";
    const APPORT_NODE = "declarationApports";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public function build()
    {
        $this->raw[self::ROOT_NODE][] = $this->getRootInfos();
        $this->raw[self::ROOT_NODE][0][self::APPORT_NODE]['produits'] = $this->getProduits();
        $this->raw[self::ROOT_NODE][0][self::SITE_NODE]['sites'] = $this->getSites();
    }

    public function getRootInfos()
    {
        return [
            'campagne' => $this->sv->campagne,
            'numeroCVICave' => $this->sv->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->sv->valide->date_saisie)
                                            ->format('d/m/Y 00:00:00'),
            self::APPORT_NODE => ['produits' => []],
            self::SITE_NODE => ['sites' => []]
        ];
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
                $apporteurs[] = $this->buildInfoApporteur($cvi, $hash_produit);
            }

            $produits[] = [
                "codeProduit" => $produitFromConf->code_douane,
                "mentionValorisante" => $produit->denomination_complementaire ?: "",
                "apports" => $apporteurs
            ];
        }

        return $produits;
    }

    public function buildInfoApporteur($cvi, $hash_produit)
    {
        $apporteur = $this->sv->apporteurs->get($cvi);
        $produit = $apporteur->get(str_replace('/declaration/', '', $hash_produit))->getFirst();

        // infos globales
        $infosApporteur = [
            "numeroCVIApporteur" => $cvi,
            "zoneRecolte" => "B",
            "superficieRecolte" => "".$produit->superficie_recolte,
            "volumeApportRaisins" => "".$produit->volume_recolte,
            "volumeIssuRaisins" => "".$produit->volume_revendique,
        ];

        // volume à éliminer
        if ($produit->vci || $produit->volume_detruit) {
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
        if (strpos($hash_produit, '/CREMANT/') !== false) {
            $produitsAssocies = ['typeAssociation' => 'REB'];
            $hash_rebeche = str_replace(['/declaration/', '/cepages/PN', '/cepages/BL'], ['', '/cepages/RBRS', '/cepages/RB'], $hash_produit);
            $produitsAssocies['codeProduitAssocie'] = $this->sv->getConfiguration()->get("/declaration/".$hash_rebeche)->code_douane;

            if ($this->sv->hasRebechesInProduits()) {
                // rebeches en détail
                $rebeches = $apporteur->get($hash_rebeche)->getFirst();

                $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = "".$rebeches->volume_revendique;
            } else {
                // % des rebeches totales
                if (strpos($hash_produit, '/cepages/BL') !== false) {
                    $produitsAssocies['volumeIssuRaisinsProduitAssocie'] = (string) ($produit->volume_revendique * 100 / $this->sv->rebeches);
                }
            }

            $infosApporteur['produitsAssocies'][] = $produitsAssocies;
        }

        return $infosApporteur;
    }

    public function getSites()
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
                        'codeProduit' => $code_produit,
                        'mentionValorisante' => $mention ?: "",
                        'volumeObtenu' => "".$produitsLieu[$hash]
                    ];

                    $sites[$lieu->numero]['produits'][] = $add;
                }
            }
        }

        return array_values($sites);
    }
}
