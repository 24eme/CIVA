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
}