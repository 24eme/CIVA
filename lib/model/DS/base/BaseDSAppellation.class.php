<?php
/**
 * BaseDSAppellation
 * 
 * Base model for DSAppellation

 * @property string $appellation
 * @property float $total_stock
 * @property DSMention $mention

 * @method string getAppellation()
 * @method string setAppellation()
 * @method float getTotalStock()
 * @method float setTotalStock()
 * @method DSMention getMention()
 * @method DSMention setMention()
 
 */

abstract class BaseDSAppellation extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSAppellation';
    }
                
}