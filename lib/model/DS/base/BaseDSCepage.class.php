<?php
/**
 * BaseDSCepage
 * 
 * Base model for DSCepage

 * @property string $libelle
 * @property string $no_vtsgn
 * @property acCouchdbJson $detail

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getNoVtsgn()
 * @method string setNoVtsgn()
 * @method acCouchdbJson getDetail()
 * @method acCouchdbJson setDetail()
 
 */

abstract class BaseDSCepage extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCepage';
    }
                
}