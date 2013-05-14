<?php
/**
 * BaseDSAppellation
 * 
 * Base model for DSAppellation

 * @property string $appellation
 * @property float $total_stock
 * @property float $total_vt
 * @property float $total_sgn
 * @property float $total_normal
 * @property DSMention $mention

 * @method string getAppellation()
 * @method string setAppellation()
 * @method float getTotalStock()
 * @method float setTotalStock()
 * @method float getTotalVt()
 * @method float setTotalVt()
 * @method float getTotalSgn()
 * @method float setTotalSgn()
 * @method float getTotalNormal()
 * @method float setTotalNormal()
 * @method DSMention getMention()
 * @method DSMention setMention()
 
 */

abstract class BaseDSAppellation extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSAppellation';
    }
                
}