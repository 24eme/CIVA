<?php
/**
 * BaseDSLieu
 * 
 * Base model for DSLieu

 * @property float $total_stock

 * @method float getTotalStock()
 * @method float setTotalStock()
 
 */

abstract class BaseDSLieu extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSLieu';
    }
                
}