<?php

class ExportSVCsv
{
    private $cvi = null;
    private $apporteurs = [];

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

        if ($with_header) {
            fputcsv($stream, $this->getHeader(), ';');
        }

        foreach ($this->getDocs($campagne) as $sv) {
            if(is_string($sv)) {
                $sv = SVClient::getInstance()->find($sv);
            }
            if(!count($sv->getProduits())) {
                fputcsv($stream, [
                    $sv->declarant->cvi,$sv->declarant->raison_sociale,null,null,null,null,null,null,null,null,null,null,null,null,null,$sv->type,$sv->valide->date_saisie,null,$sv->_id], ';');
            }
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

        $volume_recolte = $produit->volume_recolte;

        if($produit->exist('volume_mouts') && $produit->volume_mouts)) {
            $volume_recolte += $produit->volume_mouts;
        }

        return [
            $sv->declarant->cvi,
            $sv->declarant->raison_sociale,
            $cvi_apporteur,
            $this->getApporteur($cvi_apporteur)->raison_sociale,
            $produit->getLibelle(),
            $produit->getConfig()->getLieu()->getLibelle(),
            $produit->getConfig()->getCepage()->getLibelle(),
            $produit->getConfig()->getMention()->getLibelle(),
            $produit->denomination_complementaire,
            $produit->superficie_recolte + $produit->superficie_mouts,
            $produit->quantite_recolte,
            $volume_recolte,
            $produit->volume_detruit,
            $produit->vci,
            $produit->volume_revendique + $produit->volume_mouts_revendique,

            $sv->type,
            $sv->valide->date_saisie,
            substr($hashproduit, 22),
            $sv->_id,
        ];
    }

    public function getDocs($campagne) {
        $docs = [];

        if ($this->cvi) {
            $etablissement = EtablissementClient::getInstance()->findByCvi($this->cvi);
            $docs[] = SVClient::getInstance()->findByIdentifiantAndCampagne($etablissement->identifiant, $campagne);
        } else {
            $docs = SVClient::getInstance()->getAllIdsByCampagne($campagne);
        }

        return $docs;
    }

    public function getApporteur($cvi)
    {
        if (array_key_exists($cvi, $this->apporteurs)) {
            return $this->apporteurs[$cvi];
        }

        $apporteur = EtablissementClient::getInstance()->findByCvi($cvi);
        $this->apporteurs[$cvi] = $apporteur;

        return $this->apporteurs[$cvi];
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
