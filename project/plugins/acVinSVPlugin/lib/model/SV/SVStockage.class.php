<?php
/**
 * Model for SVApporteur
 *
 */

class SVStockage extends BaseSVStockage {
    public function isPrincipale() {
        $principale = $this->getDocument()->stockage->getFirst();

        return $this->numero == $principale->numero;
    }

    public function getProduits() {
        if(!$this->isPrincipale() && $this->exist('produits')) {

            return $this->_get('produits');
        }

        if(!$this->isPrincipale()) {
            return [];
        }

        $produits = [];
        foreach($this->getDocument()->getRecapProduits() as $hash => $produit) {
            $produits[$hash] = $produit->volume_revendique + $produit->volume_mouts_revendique;
            foreach($this->getDocument()->stockage as $stockage) {
                if(!$stockage->exist('produits') || !$stockage->_get('produits')->exist($hash)) {
                    continue;
                }
                $produits[$hash] -= $stockage->_get('produits')->get($hash);
            }
        }

        foreach($produits as $hash => $volume) {
            $produits[$hash] = round($volume, 2);
        }

        return $produits;
    }
}
