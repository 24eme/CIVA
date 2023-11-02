<?php


abstract class BaseSVExtraction extends acCouchdbDocumentTree {

    public function configureTree() {
       $this->_root_class_name = 'SV';
       $this->_tree_class_name = 'SVExtraction';
    }

}
