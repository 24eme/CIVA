<?php
/**
 * BaseVracDeclaration
 * 
 * Base model for VracDeclaration


 
 */

abstract class BaseVracDeclaration extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracDeclaration';
    }
                
}