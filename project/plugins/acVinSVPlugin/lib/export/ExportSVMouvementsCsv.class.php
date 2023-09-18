<?php

class ExportSVMouvementsCsv extends ExportSVCsv
{

    public function generate($campagne, $with_header = true)
    {
        $stream = fopen('php://output', 'w');

        if ($stream === false) {
            throw new Exception("Impossible d'ouvrir le flux");
        }

        if ($with_header) {
            fputcsv($stream, $this->getHeader(), ';');
        }

        foreach ($this->getDocs($campagne) as $sv) {
            if(is_string($sv)) {
                $sv = SVClient::getInstance()->find($sv);
            }
            foreach ($sv->getProduits() as $produit) {
                $lines = $this->build($sv, $produit);
                foreach($lines as $line) {
                    fputcsv($stream, $line, ';');
                }
            }
        }

        fclose($stream);
    }

    public function buildLine(SV $sv, SVProduit $produit, $type_mouvement, $quantite) {
        $hashproduit = $produit->getHash();
        $cvi_apporteur = explode("/", $hashproduit)[2];

        return [
            $sv->type,
            $sv->periode,
            $sv->declarant->cvi,
            $sv->identifiant,
            $sv->declarant->raison_sociale,
            $produit->getConfig()->getAppellation()->getLibelle(),
            $produit->getConfig()->getLieu()->getLibelle(),
            $produit->getConfig()->getCepage()->getLibelle(),
            $produit->getConfig()->getMention()->getLibelle(),
            "",
            $produit->denomination_complementaire,
            $type_mouvement,
            $quantite,
            $cvi_apporteur,
            $this->getApporteur($cvi_apporteur),
            substr($hashproduit, 22),
            $sv->_id,
            $sv->getFamilleCalculee(),
        ];
    }

    public function build(SV $sv, SVProduit $produit)
    {
        return [
        $this->buildLine($sv, $produit, "superficie", $produit->superficie_recolte),
        $this->buildLine($sv, $produit, "quantite", $produit->quantite_recolte),
        $this->buildLine($sv, $produit, "volume", $produit->volume_recolte),
        $this->buildLine($sv, $produit, "vci", $produit->vci),
        $this->buildLine($sv, $produit, "volume_revendique", $produit->vci),
        ];
    }

    public function getHeader()
    {
        return [
            "type", "année", "cvi", "identifiant", "nom", "appellation", "lieu", "cepage",
            "vtsgn", "lieudit", "denomination", "type mouvement", "quantite", "identifiant acheteur", "cvi acheteur",
            "nom acheteur", "hash produit", "doc id", "famille calculee",
        ];
    }
}
