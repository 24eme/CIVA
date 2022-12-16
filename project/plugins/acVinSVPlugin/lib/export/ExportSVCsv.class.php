<?php

class ExportSVCsv
{
    private $cvi = null;

    public function __construct($cvi = null)
    {
        $this->cvi = $cvi;
    }

    public function generate($campagne, $with_header = true)
    {
        $stream = fopen('php://output', 'w');

        if ($stream === false) {
            throw new Exception("Impossible d'ouvrir le flux");
        }

        $toExport = [];

        if ($this->cvi) {
            $etablissement = EtablissementClient::getInstance()->findByCvi($this->cvi);
            $toExport[] = SVClient::getInstance()->findByIdentifiantAndCampagne($etablissement->identifiant, $campagne);
        } else {
            $toExport = SVClient::getInstance()->getAll($campagne);
        }

        if ($with_header) {
            fputcsv($stream, $this->getHeader(), ';');
        }

        foreach ($toExport as $sv) {
            foreach ($sv->getProduits() as $produit) {
                fputcsv($stream, $this->build($sv, $produit), ';');
            }
        }

        fclose($stream);
    }

    public function build(SV $sv, SVProduit $produit)
    {
        $hashproduit = $produit->getHash();
        $cvi_apporteur = explode("/", $hashproduit)[2];

        return [
            $sv->declarant->cvi,
            $sv->declarant->raison_sociale,
            $cvi_apporteur,
            $this->getApporteur($cvi_apporteur),
            $produit->getLibelle(),
            $produit->getConfig()->getLieu()->getLibelle(),
            $produit->getConfig()->getCepage()->getLibelle(),
            $produit->getConfig()->getMention()->getLibelle(),
            $produit->denomination_complementaire,
            $produit->superficie_recolte,
            $produit->quantite_recolte,
            $produit->volume_recolte,
            $produit->volume_detruit,
            $produit->vci,
            $produit->volume_revendique,

            $sv->type,
            $sv->valide->date_saisie,
            substr($hashproduit, 22),
            $sv->_id,
        ];
    }

    public function getApporteur($cvi)
    {
        if (array_key_exists($cvi, $this->apporteurs)) {
            return $this->apporteurs[$cvi]->raison_sociale;
        }

        $apporteur = EtablissementClient::getInstance()->findByCvi($cvi);
        $this->apporteurs[$cvi] = $apporteur;

        return $this->apporteurs[$cvi]->raison_sociale;
    }

    public function getHeader()
    {
        return [
            "CVI acheteur", "Nom acheteur", "CVI récoltant", "Nom récoltant", "Appellation", "Lieu", "Cépage", "VTSGN",
            "Dénomination", "Superficie livrée", "Qté livrée en kg", "Volume livré", "Volume à détruire", "Dont VCI",
            "Volume revendiqué", "Type", "Date de validation", "Hash produit", "Document ID"
        ];
    }
}
