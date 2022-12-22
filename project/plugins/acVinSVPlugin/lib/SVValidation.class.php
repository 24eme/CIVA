<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'apporteurs_complet', "Toutes les données des apporteurs n'ont pas été saisies");
        $this->addControle('erreur', 'apporteurs_coherent_sv11', "Il y a des incohérences entre le volume récolté, revendiqué, détruit et le VCI");
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
        $this->addControle('erreur', 'rebeches_cremant_manquant', "Vous avez déclaré des rebêches alors que vous ne produisez pas de Crémant");
        $this->addControle('erreur', 'cremant_rebeches_manquant', "Vous n'avez pas déclaré de rebêches alors que vous produisez du Crémant");
        $this->addControle('vigilance', 'lies_vides', "Vous n'avez pas déclaré de lies et bourbes");
    }

    public function controle()
    {
        foreach($this->document->apporteurs as $apporteur) {
            if(!$apporteur->isComplete()) {
                $this->addPoint('erreur', 'apporteurs_complet', "Terminer la saisie", $this->generateUrl('sv_apporteurs', $this->document));
                break;
            }

            foreach($apporteur->getProduits() as $produit) {
                if($this->document->type != SVClient::TYPE_SV11) {
                    continue;
                }
                if($produit->isRebeche()) {
                    continue;
                }
                if(round($produit->volume_recolte,  2) == round($produit->volume_revendique + $produit->volume_detruit, 2)) {
                    continue;
                }
                if(round($produit->volume_vci, 2) < round($produit->volume_detruit, 2)) {
                    continue;
                }
                $this->addPoint('erreur', 'apporteurs_coherent_sv11', $apporteur->getNom().', '.$produit->getLibelle(), $this->generateUrl('sv_saisie', array('sf_subject' => $this->document, 'cvi' => $apporteur->getKey())));
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
            $this->addPoint('vigilance', 'lies_vides', 'Saisir les lies et bourbes', $this->generateUrl('sv_autres', $this->document));
        }

        // si on n'a pas de crémant mais des rebêches
        if (!$this->document->hasCremantInProduits() && $this->document->rebeches) {
           $this->addPoint('erreur', 'rebeches_cremant_manquant', 'Modifier les rebêches', $this->generateUrl('sv_autres', $this->document));
        }

        // si on n'a pas de rebeches mais du crémant
        if ($this->document->hasVolumeCremantInProduits() && !$this->document->rebeches) {
           $this->addPoint('erreur', 'cremant_rebeches_manquant', 'Saisir les rebêches', $this->generateUrl('sv_autres', $this->document));
        }
    }
}
