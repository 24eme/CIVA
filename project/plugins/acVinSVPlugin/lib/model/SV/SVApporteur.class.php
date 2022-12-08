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
            if(!$produit->isComplete()) {
                continue;
            }
            $nbSaisies++;
        }

        return $nbSaisies;
    }

    public function getRecapProduits()
    {
        return array_reduce($this->getProduits(), function ($recap, $p) {
            $recap['superficie'] += $p->superficie_recolte;
            $recap['revendique'] += $p->volume_revendique;

            return $recap;
        }, ['superficie' => 0, 'revendique' => 0]);
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
    }
}