<?php
/**
 * BaseSVApporteur
 *
 * Base model for SVApporteur



 */

abstract class BaseSVStockage extends acCouchdbDocumentTree {

    public function configureTree() {
       $this->_root_class_name = 'SV';
       $this->_tree_class_name = 'SVStockage';
    }

}