<?php
/**
 * BaseVracLieu
 * 
 * Base model for VracLieu

 * @property string $libelle
 * @property string $libelle_long

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getLibelleLong()
 * @method string setLibelleLong()
 
 */

abstract class BaseVracLieu extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracLieu';
    }
                
}