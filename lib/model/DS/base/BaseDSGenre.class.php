<?php
/**
 * BaseDSGenre
 * 
 * Base model for DSGenre

 * @property float $total_stock

 * @method float getTotalStock()
 * @method float setTotalStock()
 
 */

abstract class BaseDSGenre extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSGenre';
    }
                
}