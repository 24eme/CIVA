<?php

class ExportSV12Json extends ExportSVJson
{
    const ROOT_NODE = "declarationsProductionsNegociants";
    const APPORT_NODE = "declarationAchats";
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
            'numeroCVINegociant' => $this->sv->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->sv->valide->date_saisie)
                                            ->format('d/m/Y 00:00:00'),
            'volumeLies' => "".$this->sv->lies,
            self::APPORT_NODE => ['produits' => []],
            self::SITE_NODE => ['sites' => []]
        ];
    }

    public function getProduits()
    {
        $produits = [];
        $fournisseursParProduit = $this->sv->getApporteursParProduit();

        foreach ($fournisseursParProduit as $hash_produit => $fournisseurs_du_produit) {
            if (strpos($hash_produit, '/cepages/RB') !== false) {
                continue; // pas les rebêches dans la boucle principale
            }

            $fournisseurs = [];

            // pour le code_douane
            $produitFromConf = $this->sv->getConfiguration()->get($hash_produit);

            foreach ($fournisseurs_du_produit as $cvi) {
                $apporteur = $this->sv->apporteurs->get($cvi);
                $produit = $apporteur->get(str_replace('/declaration/', '', $hash_produit))->getFirst();

                $fournisseurs[] = $this->buildInfoFournisseur($produit, $hash_produit);

                if ($produit->exist('volume_mouts')) {
                    $lastFournisseur = $fournisseurs[array_key_last($fournisseurs)];
                    unset($lastFournisseur['quantiteAchatRaisins']);
                    unset($lastFournisseur['volumeIssuRaisins']);
                    unset($lastFournisseur['produitsAssocies']);

                    $lastFournisseur['volumeAchatMouts'] = number_format($produit->volume_mouts, 2, ".", "");
                    $lastFournisseur['volumeIssuMouts'] = number_format($produit->volume_mouts_revendique, 2, ".", "");

                    $fournisseurs[] = $lastFournisseur;
                }
            }

            $produits[] = [
                "codeProduit" => $produitFromConf->code_douane,
                "mentionValorisante" => $produit->denomination_complementaire ?: "",
                "fournisseurs" => $fournisseurs
            ];
        }

        return $produits;
    }

    public function buildInfoFournisseur($produit, $hash_produit)
    {
        // infos globales
        $infosApporteur = [
            "numeroEvvFournisseur" => $produit->cvi,
            "zoneRecolte" => "B",
            "superficieRecolte" => number_format($produit->superficie_recolte, 2, ".", ""),
            "quantiteAchatRaisins" => number_format($produit->quantite_recolte, 0, ".", ""),
            "volumeIssuRaisins" => number_format($produit->volume_revendique, 2, ".", "")
        ];

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
