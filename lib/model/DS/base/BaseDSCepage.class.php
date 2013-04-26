<?php
/**
 * BaseDSCepage
 * 
 * Base model for DSCepage

 * @property string $appellation
 * @property string $cepage
 * @property acCouchdbJson $detail

 * @method string getAppellation()
 * @method string setAppellation()
 * @method string getCepage()
 * @method string setCepage()
 * @method acCouchdbJson getDetail()
 * @method acCouchdbJson setDetail()
 
 */

abstract class BaseDSCepage extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCepage';
    }
                
}