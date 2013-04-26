<?php
/**
 * BaseDSCouleur
 * 
 * Base model for DSCouleur

 * @property float $total_stock

 * @method float getTotalStock()
 * @method float setTotalStock()
 
 */

abstract class BaseDSCouleur extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCouleur';
    }
                
}