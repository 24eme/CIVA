<?php
/**
 * BaseDSMention
 * 
 * Base model for DSMention

 * @property float $total_stock

 * @method float getTotalStock()
 * @method float setTotalStock()
 
 */

abstract class BaseDSMention extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSMention';
    }
                
}