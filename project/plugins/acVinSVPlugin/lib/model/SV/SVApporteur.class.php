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

    public function getCvi() {

        return $this->getFirst()->getFirst()->cvi;
    }

    public function getNom() {

        return $this->getFirst()->getFirst()->nom;
    }

    public function getCommune() {

        return $this->getFirst()->getFirst()->commune;
    }
}