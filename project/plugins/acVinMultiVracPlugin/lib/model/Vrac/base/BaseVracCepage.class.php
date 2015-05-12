<?php
/**
 * BaseVracCepage
 * 
 * Base model for VracCepage

 * @property string $libelle
 * @property string $libelle_long
 * @property string $no_vtsgn
 * @property acCouchdbJson $detail

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getLibelleLong()
 * @method string setLibelleLong()
 * @method string getNoVtsgn()
 * @method string setNoVtsgn()
 * @method acCouchdbJson getDetail()
 * @method acCouchdbJson setDetail()
 
 */

abstract class BaseVracCepage extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracCepage';
    }
                
}