<?php

class ExportSV11Json extends ExportSVJson
{
    const ROOT_NODE = "declarationsProductionCaves";
    const APPORT_NODE = "declarationApports";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public $HAS_MOUTS = false;
    public $HAS_VOLUME_A_ELIMINER = true;

    public $PRODUITS_APPORTEUR_NODE = "apports";
    public $NUMERO_APPORTEUR = "numeroCVIApporteur";
    public $APPORT_RAISIN = "volumeApportRaisins";

    public function build()
    {
        $root = $this->getRootInfos();
        $root[self::APPORT_NODE]['produits'] = $this->getProduits();
        $root[self::SITE_NODE]['sites'] = $this->getSites();

        $this->raw = $root;
    }

    public function getRootInfos()
    {
        $infos = [
            'campagne' => $this->sv->campagne,
            'numeroCVICave' => $this->sv->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->sv->valide->date_saisie)
                                            ->format('d/m/Y 00:00:00'),
            self::APPORT_NODE => ['produits' => []],
            self::SITE_NODE => ['sites' => []]
        ];

        if ($this->sv->exist('motif_modification') && $this->sv->motif_modification) {
            $motif = ['code' => $this->sv->motif_modification->motif];

            if ($motif['code'] === SV::SV_MOTIF_MODIFICATION_AUTRE) {
                $motif['libelleAutre'] = $this->sv->motif_modification->libelle;
            }

            $infos['motifModification'] = $motif;
        }

        return $infos;
    }

    public function getApportRaisin($produit)
    {
        return number_format($produit->volume_recolte, 2, ".", "");
    }
}
