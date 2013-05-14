<?php
/**
 * BaseDSCepage
 * 
 * Base model for DSCepage

 * @property string $appellation
 * @property string $cepage
 * @property string $no_vtsgn
 * @property string $lieu
 * @property acCouchdbJson $detail

 * @method string getAppellation()
 * @method string setAppellation()
 * @method string getCepage()
 * @method string setCepage()
 * @method string getNoVtsgn()
 * @method string setNoVtsgn()
 * @method string getLieu()
 * @method string setLieu()
 * @method acCouchdbJson getDetail()
 * @method acCouchdbJson setDetail()
 
 */

abstract class BaseDSCepage extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCepage';
    }
                
}