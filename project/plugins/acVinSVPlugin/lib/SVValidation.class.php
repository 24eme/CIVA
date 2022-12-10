<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'apporteurs_complet', "Toutes les données des apporteurs n'ont pas été saisies");
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
        $this->addControle('erreur', 'lies_vides', "Les lies n'ont pas été remplies");
        $this->addControle('erreur', 'rebeches_vides', "Les rebêches n'ont pas été remplies");
        $this->addControle('erreur', 'rebeches_incoherentes', "Les rebêches totales ne correspondent pas au détail");
        $this->addControle('erreur', 'rebeches_cremant_manquant', "Il y a des rebêches alors qu'il n'y a pas de crémant");
        $this->addControle('erreur', 'cremant_rebeches_manquant', "Il y a du crémant alors qu'il n'y a pas de rebêches");
    }

    public function controle()
    {
        foreach($this->document->apporteurs as $apporteur) {
            if(!$apporteur->isComplete()) {
                $this->addPoint('erreur', 'apporteurs_complet', '');
                break;
            }
        }

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

        if ($this->document->hasRebechesInProduits()) {
           if ($this->document->calculateRebeches() !== $this->document->rebeches) {
               $this->addPoint('erreur', 'rebeches_incoherentes', '');
           }

           // Si rebeches sans crémant: si array_filter ressort 0 produits, alors pas de crémant
           if (count(array_filter($this->document->getRecapProduits(), function ($k) {
               // on filtre sur les produit crémant et qui ne sont pas cépage rebeches
               return strpos($k, '/CREMANT/') !== false && strpos($k, '/cepages/RB') === false;
           }, ARRAY_FILTER_USE_KEY)) === 0) {
               $this->addPoint('erreur', 'rebeches_cremant_manquant', "");
           }
        }

        if ($this->document->hasRebechesInProduits() === false) {
            // si on n'a pas de rebeches mais du crémant
            if (count(array_filter($this->document->getRecapProduits(), function ($k) {
                // on filtre sur les produit crémant et qui ne sont pas cépage rebeches
                return strpos($k, '/CREMANT/') !== false && strpos($k, '/cepages/RB') === false;
            }, ARRAY_FILTER_USE_KEY)) > 0) {
               $this->addPoint('erreur', 'cremant_rebeches_manquant', "");
            }
        }
    }
}
