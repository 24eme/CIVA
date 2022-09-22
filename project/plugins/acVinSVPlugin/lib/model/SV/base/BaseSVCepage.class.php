<?php
/**
 * BaseSVCepage
 * 
 * Base model for SVCepage


 
 */

abstract class BaseSVCepage extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'SV';
       $this->_tree_class_name = 'SVCepage';
    }
                
}