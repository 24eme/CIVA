<?php

class SVValidation extends DocumentValidation
{
    public function configure()
    {
        $this->addControle('erreur', 'apporteurs_complet', "Toutes les données des apporteurs n'ont pas été saisies");
        $this->addControle('erreur', 'apporteurs_coherent_sv11', "Il y a des incohérences entre le volume récolté, revendiqué, détruit et le VCI");
        $this->addControle('erreur', 'volume_extrait', "Le volume revendiqué total n'est pas égal au volume extrait déclaré");
        $this->addControle('erreur', 'stockage_repartition', "Trop de volume déclaré dans le lieu de stockage");
        $this->addControle('erreur', 'rebeches_cremant_manquant', "Vous avez déclaré des rebêches alors que vous ne produisez pas de Crémant");
        $this->addControle('erreur', 'cremant_rebeches_manquant', "Vous n'avez pas déclaré de rebêches alors que vous produisez du Crémant");
        $this->addControle('erreur', 'superficie_mouts_incoherent', "La superficie des mouts doit être supérieure à zéro");
        $this->addControle('vigilance', 'lies_vides', "Vous n'avez pas déclaré de lies et bourbes");
    }

    public function controle()
    {
        foreach($this->document->apporteurs as $apporteur) {
            if(!$apporteur->isComplete()) {
                $this->addPoint('erreur', 'apporteurs_complet', "Terminer la saisie", $this->generateUrl('sv_apporteurs', $this->document));
                break;
            }

            if($this->document->type == SVClient::TYPE_SV12) {
                foreach($this->document->getRecapProduits() as $hash => $recapProduit) {
                    if(!$this->document->extraction->exist(str_replace('/declaration/', '', $hash))) {
                        continue;
                    }
                    $extractionProduit = $this->document->extraction->get(str_replace('/declaration/', '', $hash));
                    if($extractionProduit->volume_extrait && $extractionProduit->volume_extrait != $recapProduit->volume_revendique) {
                        $this->addPoint('erreur', 'volume_extrait', $recapProduit->libelle, $this->generateUrl('sv_revendication', ['sf_subject' => $this->document]));
                    }
                }

                foreach($apporteur->getProduits() as $produit) {
                    if ($produit->isMout() && $produit->superficie_mouts <= 0) {
                        $this->addPoint('erreur', 'superficie_mouts_incoherent', "Saisir la superficie", $this->generateUrl(
                            'sv_saisie', ['sf_subject' => $this->document, 'cvi' => $apporteur->getKey()]
                        ));
                    }
                }

                continue;
            }

            foreach($apporteur->getProduits() as $produit) {
                if($produit->isRebeche()) {
                    continue;
                }
                if(round($produit->volume_recolte,  2) == round($produit->volume_revendique + $produit->volume_detruit, 2)) {
                    continue;
                }
                $vci = 0;
                if ($produit->exist('volume_vci')) {
                    $vci = $produit->volume_vci;
                }
                if(round($vci, 2) < round($produit->volume_detruit, 2)) {
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
