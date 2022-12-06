<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
        $this->addControle('erreur', 'lies_vides', "Les lies n'ont pas été remplies");
        $this->addControle('erreur', 'rebeches_vides', "Les rebêches n'ont pas été remplies");
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
                    $this->addPoint('erreur', 'stockage_repartition', $stockage->numero);
                }
            }
        }

        if ($this->document->lies === null || is_numeric($this->document->lies) === false) {
            $this->addPoint('erreur', 'lies_vides', '');
        }

        if ($this->document->rebeches === null || is_numeric($this->document->rebeches) === false) {
            $this->addPoint('erreur', 'rebeches_vides', '');
        }
    }
}
