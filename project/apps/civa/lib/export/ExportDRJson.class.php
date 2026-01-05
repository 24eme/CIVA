<?php

class ExportDRJson
{
    const ROOT_NODE = "declarationsRecolteProductionRecoltants";
    const APPORT_NODE = "declarationProduitsRecoltes";
    const SITE_NODE = "declarationVolumesObtenusParSite";

    public $PRODUITS_APPORTEUR_NODE = "apports";
    public $NUMERO_APPORTEUR = "numeroCVIApporteur";

    protected $dr;
    protected $raw;
    protected $xml;

    public function __construct(DR $declaration)
    {
        $this->dr = $declaration;
        $this->xml = (new ExportDRXml($declaration, null))->getXml();
    }

    public function isValide()
    {
        return $this->dr->valide->statut === "VALIDE";
    }

    public function export()
    {
        $json = json_encode($this->raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_last_error_msg();
            return false;
        }

        return $json.PHP_EOL;
    }

    public function build()
    {
        $root = $this->getRootInfos();
        $root[self::APPORT_NODE]['produitsRecoltes'] = $this->getProduits();
        //$root[self::SITE_NODE]['sites'] = $this->getSites();

        $this->raw = $root;
    }

    public function getRootInfos()
    {
        $infos = [
            'campagne' => $this->dr->campagne.'-'.($this->dr->campagne + 1),
            'numeroCVIRecoltant' => $this->dr->declarant->cvi,
            'dateDepot' => DateTimeImmutable::createFromFormat('Y-m-d', $this->dr->validee)
                                            ->format('d/m/Y 00:00:00'),
            self::APPORT_NODE => ['produitsRecoltes' => []],
            //self::SITE_NODE => ['sites' => []]
        ];

        // Même codes que pour la SV : ER, RV, AU
        // N'existe pas dans le schéma de la DR
        if ($this->dr->exist('motif_modification') && $this->dr->motif_modification) {
            $motif = ['code' => $this->dr->motif_modification->motif];

            if ($motif['code'] === SV::SV_MOTIF_MODIFICATION_AUTRE) {
                $motif['libelleAutre'] = $this->dr->motif_modification->libelle;
            }

            $infos['motifModification'] = $motif;
        }

        return $infos;
    }

    public function getProduits()
    {
        $produits = [];

        $correspondanceNumLigneJson = [
            "L5" => "recolteTotale",
            "L9" => "conserveCaveParticuliereExploitant",
            //"L10" => "volVinifie",
            "L12" => "volNonVinifie",
            "L13" => "VolMcMcrObtenu",
            //"L15" => "volMoutApteAOP",
            "L15" => "volVinRevendicableOuCommercialisable",
            "L16" => "volDRAOuLiesSoutirees",
            "L17" => "volEauEliminee",
            "L18" => "VSI",
            "L19" => "VCI",
        ];

        foreach($this->xml as $xmlCol) {
            $produit = [];
            $produit["typeRecoltant"] = "EX"; // Y a t'il des bailleurs vinificateurs ? (code BV)
            $produit["codeProduit"] = $xmlCol["L1"];
            $produit["zoneRecolte"] = $xmlCol["L3"];
            $produit["mentionValorisante"] = isset($xmlCol["mentionVal"]) ? $xmlCol["mentionVal"] : "";
            $produit["superficieRecolte"] = number_format($xmlCol["L4"], 4, ".", "");
            if(isset($xmlCol['motifSurfZero']) && $xmlCol['motifSurfZero']) {
                $produit['motifAbsenceRecolte'] = ['codeAbsenceRecolte' => in_array($xmlCol['motifSurfZero'], ['PC', 'PS', 'IN', 'OG', 'AU']) ? $xmlCol['motifSurfZero'] : 'AU'];
                if ($produit['motifAbsenceRecolte']['codeAbsenceRecolte'] == 'AU') {
                    $produit['motifAbsenceRecolte']['motifAutreAbsenceRecolte'] = $xmlCol['motifSurfZero'];
                }
                $produit['recolteTotale'] = number_format(0, 2, ".", "");
                $produits[] = $produit;
                continue;
            }
            $produit["conserveCaveParticuliereExploitant"] = number_format(0, 2, ".", "");
            $produit["volEnVinification"] = number_format(0, 2, ".", "");
            foreach($correspondanceNumLigneJson as $xmlKey => $jsonKey) {
                if($xmlCol["exploitant"][$xmlKey]*1.0 > 0) {
                    $produit[$jsonKey] = number_format($xmlCol["exploitant"][$xmlKey], 2, ".", "");
                }
            }
            if(isset($produit["conserveCaveParticuliereExploitant"]) && floatval($produit["conserveCaveParticuliereExploitant"])) {
                $produit["volEnVinification"] = number_format($produit["conserveCaveParticuliereExploitant"], 2, ".", "");
            } else {
                unset($produit["conserveCaveParticuliereExploitant"]);
                unset($produit["volEnVinification"]);
                unset($produit["volDRAOuLiesSoutirees"]);
                unset($produit["volVinRevendicableOuCommercialisable"]);
            }
            foreach($xmlCol["exploitant"] as $xmlLigneKey => $xmlLigneValue) {
                if(preg_match("/^L6_/", $xmlLigneKey)) {
                    $vente = [
                        "numeroEvvDestinataire" => $xmlLigneValue["numCvi"]."",
                        "volObtenuIssuRaisins" => number_format($xmlLigneValue["volume"], 2, ".", ""),
                    ];
                    $produit["destinationVentesRaisins"][] = $vente;
                }
                if(preg_match("/^L7_/", $xmlLigneKey)) {
                    $vente = [
                        "numeroEvvDestinataire" => $xmlLigneValue["numCvi"]."",
                        "volObtenuIssuMouts" => number_format($xmlLigneValue["volume"], 2, ".", ""),
                    ];
                    $produit["destinationVentesMouts"][] = $vente;
                }
                if(preg_match("/^L8_/", $xmlLigneKey)) {
                    $vente = [
                        "numeroEvvCaveCoop" => $xmlLigneValue["numCvi"]."",
                        "volObtenuApportRaisins" => number_format($xmlLigneValue["volume"], 2, ".", ""),
                    ];
                    $produit["destinationApportsCaveCoop"][] = $vente;
                }
            }

            if(isset($xmlCol['colonneAss']) && isset($produit["conserveCaveParticuliereExploitant"])) {
                $produitAssocie = [
                    "typeAssociation" => "REB",
                    "codeProduitAssocie" => $xmlCol['colonneAss']['L1'],
                    "recolteTotaleProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L5'], 2, ".", ""),
                    "conserveCavePartExploitProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L9'], 2, ".", ""),
                    "volEnVinificationProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L10'], 2, ".", ""),
                    "volVinRevendicOuCommerciaProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L14'], 2, ".", ""),
                ];

                /*foreach($xmlCol['colonneAss']["exploitant"] as $xmlColAssLigneKey => $xmlColAssLigneValue) {
                    if(preg_match("/^L8_/", $xmlColAssLigneKey)) {
                        $vente = [
                            "numeroEvvCaveCoop" => $xmlColAssLigneValue["numCvi"]."",
                            "volObtenuApportRaisins" => number_format($xmlColAssLigneValue["volume"], 2, ".", ""),
                        ];
                        $produitAssocie["destinationApportsCaveCoopProdAssocie"][] = $vente;
                    }
                    }*/

                $produit["produitsAssocies"][] = $produitAssocie;
            }

            $produits[] = $produit;
        }

        return $produits;
    }

    public function addHeaders($response)
    {
        $response->setHttpHeader('Content-Type', 'application/json');
        $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $this->dr->_id . '_douane.json"');
        $response->setHttpHeader('Content-Transfer-Encoding', 'binary');
        $response->setHttpHeader('Pragma', '');
        $response->setHttpHeader('Cache-Control', 'public');
        $response->setHttpHeader('Expires', '0');
    }
}
