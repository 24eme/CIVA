<?php
/**
 * Model for DSProduit
 *
 */

class DSProduit extends BaseDSProduit {

    public function updateProduit()
    {
        $this->produit_libelle = $this->getConfig()->getLibelleFormat(array(), "%g% %a% %m% %l% %co% %ce%");
        $this->code_produit = $this->getConfig()->getCodeProduit();
    }

    public function isActif() {

        return (!is_null($this->stock_declare));
    }
    
    public function hasElaboration(){
        return strstr($this->produit_hash, 'EFF')!==false;
    }


}
