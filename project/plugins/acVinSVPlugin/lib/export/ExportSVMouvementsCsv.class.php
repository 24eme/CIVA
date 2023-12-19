<?php

class ExportSVMouvementsCsv extends ExportSVCsv
{

    public function exportOne($sv, $with_header = true) {
        $csv = "";
        if ($with_header) {
            $csv .= implode(';', $this->getHeader())."\n";
        }
        if(is_string($sv)) {
            $sv = SVClient::getInstance()->find($sv);
        }
        foreach ($sv->getProduits() as $produit) {
            $lines = $this->build($sv, $produit);
            foreach($lines as $line) {
                $csv .= implode(';', $line)."\n";
            }
        }
        return $csv;
    }

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
            $sv->identifiant,
            $sv->declarant->cvi,
            $sv->declarant->raison_sociale,
            $produit->getConfig()->getAppellation()->getLibelle(),
            $produit->getConfig()->getLieu()->getLibelle(),
            $produit->getConfig()->getCepage()->getLibelle(),
            $produit->getConfig()->getMention()->getLibelle(),
            "",
            $produit->denomination_complementaire,
            $type_mouvement,
            $quantite,
            $this->getApporteur($cvi_apporteur)->identifiant,
            $cvi_apporteur,
            $this->getApporteur($cvi_apporteur)->raison_sociale,
            substr($hashproduit, 22),
            $sv->_id,
            $sv->getFamilleCalculee(),
        ];
    }

    public function build(SV $sv, SVProduit $produit)
    {
        $volume_recolte = $produit->volume_recolte;

        if($produit->exist('volume_mouts') && $produit->volume_mouts) {
            $volume_recolte += $produit->volume_mouts;
        }

        return [
        $this->buildLine($sv, $produit, "superficie", $produit->superficie_recolte + $produit->superficie_mouts),
        $this->buildLine($sv, $produit, "quantite", $produit->quantite_recolte),
        $this->buildLine($sv, $produit, "volume", $volume_recolte),
        $this->buildLine($sv, $produit, "vci", $produit->vci),
        $this->buildLine($sv, $produit, "volume_detruit", $produit->volume_detruit),
        $this->buildLine($sv, $produit, "volume_revendique", $produit->volume_revendique + $produit->volume_mouts_revendique),
        ];
    }

    public function getHeader()
    {
        return [
            "type", "année", "identifiant", "cvi", "nom", "appellation", "lieu", "cepage",
            "vtsgn", "lieudit", "denomination", "type mouvement", "quantite", "identifiant vendeur", "cvi vendeur",
            "nom vendeur", "hash produit", "doc id", "famille calculee",
        ];
    }
}
