<?php
/**
 * Model for SVApporteur
 *
 */

class SVApporteur extends BaseSVApporteur {

    public function getProduits()
    {
        $produits = array();
        foreach($this as $items) {
            foreach($items as $item) {
                $produits[$item->getHash()] = $item;
            }
        }

        return $produits;
    }

    public function getNbSaisies() {
        $nbSaisies = 0;
        foreach($this->getProduits() as $produit) {
            if(!$produit->superficie_recolte || !$produit->quantite_recolte || !$produit->volume_revendique) {
                continue;
            }
            $nbSaisies++;
        }

        return $nbSaisies;
    }

    public function getCvi() {

        return $this->getFirst()->getFirst()->cvi;
    }

    public function getNom() {

        return $this->getFirst()->getFirst()->nom;
    }

    public function getCommune() {

        return $this->getFirst()->getFirst()->commune;
    }

    public function reorderByConf() {
        $children = array();


        foreach($this as $hashProduit => $child) {
            $children[$hashProduit] = $child->getData();
        }

        foreach($children as $hashProduit => $child) {
            $this->remove($hashProduit);
        }

        foreach($this->getDocument()->getConfiguration()->getProduits() as $hashProduit => $child) {
            $hashProduit = str_replace("/declaration/", "", $hashProduit);
            if(!array_key_exists($hashProduit, $children)) {
                continue;
            }
            $this->add($hashProduit, $children[$hashProduit]);
            unset($children[$hashProduit]);
        }
        foreach($children as $hashProduit => $child) {
            $this->add($hashProduit, $child);
        }
    }
}