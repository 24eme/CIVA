<?php
/**
 * BaseDSCepage
 * 
 * Base model for DSCepage

 * @property string $libelle
 * @property string $libelle_long
 * @property string $no_vtsgn
 * @property float $total_stock
 * @property float $total_vt
 * @property float $total_sgn
 * @property float $total_normal
 * @property acCouchdbJson $detail

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getLibelleLong()
 * @method string setLibelleLong()
 * @method string getNoVtsgn()
 * @method string setNoVtsgn()
 * @method float getTotalStock()
 * @method float setTotalStock()
 * @method float getTotalVt()
 * @method float setTotalVt()
 * @method float getTotalSgn()
 * @method float setTotalSgn()
 * @method float getTotalNormal()
 * @method float setTotalNormal()
 * @method acCouchdbJson getDetail()
 * @method acCouchdbJson setDetail()
 
 */

abstract class BaseDSCepage extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCepage';
    }
                
}