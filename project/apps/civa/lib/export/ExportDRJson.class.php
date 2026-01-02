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
            'campagne' => $this->dr->campagne,
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
            "L10" => "volEnVinification",
            "L13" => "volMcMcrObtenu",
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
                $produits[] = $produit;
                continue;
            }
            foreach($correspondanceNumLigneJson as $xmlKey => $jsonKey) {
                $produit[$jsonKey] = number_format($xmlCol["exploitant"][$xmlKey], 2, ".", "");
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
                        "numeroEvvDestinataire" => $xmlLigneValue["numCvi"]."",
                        "volObtenuApportRaisins" => number_format($xmlLigneValue["volume"], 2, ".", ""),
                    ];
                    $produit["destinationApportsCaveCoop"][] = $vente;
                }
            }

            if(isset($xmlCol['colonneAss'])) {
                $produitAssocie = [
                    "typeAssociation" => "REB",
                    "codeProduitAssocie" => $xmlCol['colonneAss']['L1'],
                    "recolteTotaleProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L5'], 2, ".", ""),
                    "conserveCavePartExploitProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L9'], 2, ".", ""),
                    "volEnVinificationProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L10'], 2, ".", ""),
                    "volVinRevendicOuCommerciaProdAssocie" => number_format($xmlCol['colonneAss']['exploitant']['L14'], 2, ".", ""),
                ];

                foreach($xmlCol['colonneAss']["exploitant"] as $xmlColAssLigneKey => $xmlColAssLigneValue) {
                    if(preg_match("/^L8_/", $xmlColAssLigneKey)) {
                        $vente = [
                            "numeroEvvDestinataire" => $xmlColAssLigneValue["numCvi"]."",
                            "volObtenuApportRaisins" => number_format($xmlColAssLigneValue["volume"], 2, ".", ""),
                        ];
                        $produitAssocie["destinationApportsCaveCoopProdAssocie"][] = $vente;
                    }
                }

                $produit["produitsAssocies"][] = $produitAssocie;
            }

            $produits[] = $produit;

        /*foreach ($this->dr->getProduitsDetails() as $hash_produit => $produit) {
            if (strpos($hash_produit, '/cepage_RB') !== false) {
                continue; // pas les rebêches dans la boucle principale
            }

            $infoProduit = $this->buildInfoProduit($produit);

            if (! $infoProduit['recolteTotale']) {
                continue;
            }

            $produits[] = $infoProduit;
            }*/
        }

        return $produits;
    }

    // Encore basé sur la SV
    protected function getSites()
    {
        $sites = [];
        foreach ($this->sv->stockage as $stockage) {
            $site = [];
            $site['codeSite'] = $stockage->numero;
            $sites[$stockage->numero] = $site;
        }

        foreach ($this->sv->getRecapProduits() as $hash => $produit) {
            if (strpos($hash, '/cepages/RB') !== false) {
                continue; // pas les rebêches dans les sites
            }

            $has_volume = $this->getApportRaisin($produit);

            if ($this->HAS_MOUTS) {
                $has_volume += $produit->volume_mouts;
            }

            if (! $has_volume) {
                continue;
            }

            $code_produit = $this->sv->getConfiguration()->get($produit->produit_hash)->code_douane;
            $mention = $produit->denominationComplementaire;

            foreach ($this->sv->stockage as $id => $lieu) {
                $produitsLieu = $lieu->produits;
                $produitsLieu = (is_array($produitsLieu) === false) ? $produitsLieu->toArray() : $produitsLieu;

                $volume = (array_key_exists($hash, $produitsLieu)) ? number_format($produitsLieu[$hash], 2, ".", "") : "0";

                $add = [
                    'codeProduit' => $this->processCodeDouane($code_produit),
                    'mentionValorisante' => $mention ?: "",
                    'volumeObtenu' => $volume
                ];

                $sites[$lieu->numero]['produits'][] = $add;
            }
        }

        return array_values($sites);
    }

    public function processCodeDouane($code_produit)
    {
        $code_produit = (strpos($code_produit, ',') === false) ? $code_produit : strstr($code_produit, ',', true);
        return $this->convertCodeDouane($code_produit);
    }

    public function convertCodeDouane($code_produit)
    {
        if ($code_produit === "1S001S 1") {
            return "1S001S";
        }

        if ($code_produit === "1R001S 1") {
            return "1R001S";
        }

        if ($code_produit === "1S001M00") {
            return "1S001M";
        }

        if ($code_produit === "1B001M00") {
            return "1B001M";
        }

        if ($code_produit === "1B070S") {
            return "1B070S09";
        }

        return str_replace(['D1', 'D2'], ['D6', 'D7'], $code_produit);
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
