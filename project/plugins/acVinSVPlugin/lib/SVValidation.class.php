<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
    }

    public function controle()
    {
        if (count($this->document->stockage) > 1) {
            foreach($this->document->stockage as $stockage) {
                $produits = is_array($stockage->getProduits()) ? $stockage->getProduits() : $stockage->getProduits()->toArray();
                $stocks_negatifs = array_filter($produits, function ($produit) {
                    return $produit < 0;
                });

                if (empty($stocks_negatifs) === false) {
                    $this->addPoint(
                        'erreur',
                        'stockage_repartition',
                        $stockage->numero
                    );
                }
            }
        }
    }
}
