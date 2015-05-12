<?php
/**
 * Model for DSProduit
 *
 */

class DSProduit extends BaseDSProduit {

    public function updateProduit($config,$vtsgn)
    {
        $this->produit_libelle = $config->getLibelleFormat(array(), "%g% %a% %m% %l% %co% %ce%");
        $this->code_produit = $config->getCodeProduit($vtsgn);
    }

    public function isActif() {

        return (!is_null($this->stock_declare));
    }
    
    public function hasElaboration(){
        return strstr($this->produit_hash, 'EFF')!==false;
    }


}
