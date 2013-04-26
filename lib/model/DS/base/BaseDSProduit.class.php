<?php
/**
 * BaseDSProduit
 * 
 * Base model for DSProduit

 * @property string $code_produit
 * @property string $produit_libelle
 * @property string $produit_hash
 * @property float $stock_initial
 * @property float $stock_declare
 * @property float $vci
 * @property float $reserve_qualitative
 * @property float $stock_elaboration
 * @property string $lieu
 * @property string $vt
 * @property string $sgn

 * @method string getCodeProduit()
 * @method string setCodeProduit()
 * @method string getProduitLibelle()
 * @method string setProduitLibelle()
 * @method string getProduitHash()
 * @method string setProduitHash()
 * @method float getStockInitial()
 * @method float setStockInitial()
 * @method float getStockDeclare()
 * @method float setStockDeclare()
 * @method float getVci()
 * @method float setVci()
 * @method float getReserveQualitative()
 * @method float setReserveQualitative()
 * @method float getStockElaboration()
 * @method float setStockElaboration()
 * @method string getLieu()
 * @method string setLieu()
 * @method string getVt()
 * @method string setVt()
 * @method string getSgn()
 * @method string setSgn()
 
 */

abstract class BaseDSProduit extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSProduit';
    }
                
}