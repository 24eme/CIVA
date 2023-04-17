<?php

class ExportSV12Json extends ExportSVJson
{
    const HAS_MOUTS = true;
    const HAS_VOLUME_A_ELIMINER = false;

    const ROOT_NODE = "declarationsProductionsNegociants";
    const APPORT_NODE = "declarationAchats";
    const PRODUITS_APPORTEUR_NODE = "fournisseurs";

    const NUMERO_APPORTEUR = "numeroEvvFournisseur";
    const APPORT_RAISIN = "quantiteAchatRaisins";

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

    public function getApportRaisin($produit)
    {
        return number_format($produit->quantite_recolte, 0, ".", "");
    }
}
