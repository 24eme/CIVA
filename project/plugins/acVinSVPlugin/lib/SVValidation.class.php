<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'apporteurs_complet', "Toutes les données des apporteurs n'ont pas été saisies");
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
        $this->addControle('erreur', 'lies_vides', "Vous n'avez pas déclaré de lies et bourbes");
        $this->addControle('erreur', 'rebeches_cremant_manquant', "Vous avez déclaré des rebêches alors que vous ne produisez pas de Crémant");
        $this->addControle('erreur', 'cremant_rebeches_manquant', "Vous n'avez pas déclaré de rebêches alors que vous produisez du Crémant");
    }

    public function controle()
    {
        foreach($this->document->apporteurs as $apporteur) {
            if(!$apporteur->isComplete()) {
                $this->addPoint('erreur', 'apporteurs_complet', "Terminer la saisie", $this->generateUrl('sv_apporteurs', $this->document));
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
                    $this->addPoint('erreur', 'stockage_repartition', $stockage->numero, $this->generateUrl('sv_stockage', $this->document));
                }
            }
        }

        if (!$this->document->lies && count($this->document->apporteurs) > 0) {
            $this->addPoint('erreur', 'lies_vides', 'Saisir les lies et bourbes', $this->generateUrl('sv_autres', $this->document));
        }

        // si on n'a pas de crémant mais des rebêches
        if (!$this->document->hasCremantInProduits() && $this->document->rebeches) {
           $this->addPoint('erreur', 'rebeches_cremant_manquant', 'Modifier les rebêches', $this->generateUrl('sv_autres', $this->document));
        }

        // si on n'a pas de rebeches mais du crémant
        if ($this->document->hasCremantInProduits() && !$this->document->rebeches) {
           $this->addPoint('erreur', 'cremant_rebeches_manquant', 'Saisir les rebêches', $this->generateUrl('sv_autres', $this->document));
        }
    }
}
