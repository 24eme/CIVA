<?php
/**
 * BaseDSDetail
 * 
 * Base model for DSDetail

 * @property string $vtsgn
 * @property string $appellation
 * @property string $cepage
 * @property float $volume
 * @property string $lieu
 * @property float $stock_declare
 * @property float $vci
 * @property float $reserve_qualitative

 * @method string getVtsgn()
 * @method string setVtsgn()
 * @method string getAppellation()
 * @method string setAppellation()
 * @method string getCepage()
 * @method string setCepage()
 * @method float getVolume()
 * @method float setVolume()
 * @method string getLieu()
 * @method string setLieu()
 * @method float getStockDeclare()
 * @method float setStockDeclare()
 * @method float getVci()
 * @method float setVci()
 * @method float getReserveQualitative()
 * @method float setReserveQualitative()
 
 */

abstract class BaseDSDetail extends DSProduit {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSDetail';
    }
                
}