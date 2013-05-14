<?php
/**
 * BaseDSDeclaration
 * 
 * Base model for DSDeclaration


 
 */

abstract class BaseDSDeclaration extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSDeclaration';
    }
                
}