<?php

class ExportSV11Json extends ExportSVJson
{
    const ROOT_NODE = "declarationsProductionCaves";
    const APPORT_NODE = "declarationApports";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public function build()
    {
        $root = $this->getRootInfos();
        $root[self::APPORT_NODE]['produits'] = $this->getProduits();
        $root[self::SITE_NODE]['sites'] = $this->getSites();

        $this->raw = $root;
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
                $apporteur = $this->sv->apporteurs->get($cvi);
                $produit = $apporteur->get(str_replace('/declaration/', '', $hash_produit))->getFirst();

                $apporteurs[] = $this->buildInfoApporteur($produit, $hash_produit);
            }

            $produits[] = [
                "codeProduit" => $produitFromConf->code_douane,
                "mentionValorisante" => $produit->denomination_complementaire ?: "",
                "apports" => $apporteurs
            ];
        }

        return $produits;
    }

    public function buildInfoApporteur($produit, $hash_produit)
    {
        // infos globales
        $infosApporteur = [
            "numeroCVIApporteur" => $produit->cvi,
            "zoneRecolte" => "B",
            "superficieRecolte" => number_format($produit->superficie_recolte, 2, ".", ""),
            "volumeApportRaisins" => number_format($produit->volume_recolte, 2, ".", ""),
            "volumeIssuRaisins" => number_format($produit->volume_revendique, 2, ".", "")
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
        if (strpos($hash_produit, '/CREMANT/') !== false && $produit->volume_revendique) {
            $produitsAssocies = ['typeAssociation' => 'REB'];
            $cepage = strrchr($hash_produit, '/');

            if (in_array($cepage, ['/RS', '/PN'])) {
                //si crémant rosé, on cherche les rebeches rosées
                $hash_rebeche = str_replace($cepage, '/RBRS', $hash_produit);
            } else {
                $hash_rebeche = str_replace($cepage, '/RBBL', $hash_produit);
            }

            $produitsAssocies['codeProduitAssocie'] = $this->sv->getConfiguration()->get($hash_rebeche)->code_douane;

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
}
