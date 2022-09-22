<?php
/**
 * BaseSVApporteurs
 * 
 * Base model for SVApporteurs


 
 */

abstract class BaseSVApporteurs extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'SV';
       $this->_tree_class_name = 'SVApporteurs';
    }
                
}