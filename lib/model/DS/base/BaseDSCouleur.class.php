<?php
/**
 * BaseDSCouleur
 * 
 * Base model for DSCouleur

 * @property float $total_stock
 * @property float $total_vt
 * @property float $total_sgn
 * @property float $total_normal

 * @method float getTotalStock()
 * @method float setTotalStock()
 * @method float getTotalVt()
 * @method float setTotalVt()
 * @method float getTotalSgn()
 * @method float setTotalSgn()
 * @method float getTotalNormal()
 * @method float setTotalNormal()
 
 */

abstract class BaseDSCouleur extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCouleur';
    }
                
}