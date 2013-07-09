<?php

class _TiersLieuStockage extends Base_TiersLieuStockage {
    
    public function getNumeroIncremental() {
        if(!preg_match("/^([0-9]{10})([0-9]{3})$/", $this->numero, $matches)) {
             throw new sfException("Numéro de stockage mal formé");
        }

        return $matches[2];
    }

}