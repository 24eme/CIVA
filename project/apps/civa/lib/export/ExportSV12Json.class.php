<?php

class ExportSV12Json extends ExportSVJson
{
    const ROOT_NODE = "declarationsProductionsNegociants";
    const APPORT_NODE = "declarationAchats";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public $HAS_MOUTS = true;
    public $HAS_VOLUME_A_ELIMINER = false;

    public $PRODUITS_APPORTEUR_NODE = "fournisseurs";
    public $NUMERO_APPORTEUR = "numeroEvvFournisseur";
    public $APPORT_RAISIN = "quantiteAchatRaisins";

    public function build()
    {
        $root = $this->getRootInfos();
        $root[self::APPORT_NODE]['produits'] = $this->getProduits();
        $root[self::SITE_NODE]['sites'] = $this->getSites();

        if (empty($root[self::SITE_NODE]['sites'])) {
            unset($root[self::SITE_NODE]);
        }

        $this->raw = $root;
    }

    public function getRootInfos()
    {
        $infos = [
            'campagne' => $this->sv->campagne,
            'numeroCVINegociant' => $this->sv->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->sv->valide->date_saisie)
                                            ->format('d/m/Y 00:00:00'),
            'volumeLies' => "".$this->sv->lies,
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
        return number_format($produit->quantite_recolte, 0, ".", "");
    }
}
