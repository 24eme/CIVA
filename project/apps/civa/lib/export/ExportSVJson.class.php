<?php

class ExportSVJson
{
    protected $sv;
    protected $raw;

    public function __construct(SV $declaration)
    {
        $this->sv = $declaration;
    }

    public function isValide()
    {
        return $this->sv->valide->statut === "VALIDE";
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

    protected function getSites()
    {
        $sites = [];
        foreach ($this->sv->getNotEmptyLieuxStockage() as $stockage) {
            $site = [];
            $site['codeSite'] = $stockage->numero;
            $sites[$stockage->numero] = $site;
        }

        foreach ($this->sv->getRecapProduits() as $hash => $produit) {
            $code_produit = $this->sv->getConfiguration()->get($produit->produit_hash)->code_douane;
            $mention = $produit->denominationComplementaire;

            foreach ($this->sv->getNotEmptyLieuxStockage() as $id => $lieu) {
                $produitsLieu = $lieu->produits;
                $produitsLieu = (is_array($produitsLieu) === false) ? $produitsLieu->toArray() : $produitsLieu;
                if (array_key_exists($hash, $produitsLieu)) {
                    $add = [
                        'codeProduit' => $code_produit,
                        'mentionValorisante' => $mention ?: "",
                        'volumeObtenu' => number_format($produitsLieu[$hash], 2, ".", "")
                    ];

                    $sites[$lieu->numero]['produits'][] = $add;
                }
            }
        }

        return array_values($sites);
    }
}
