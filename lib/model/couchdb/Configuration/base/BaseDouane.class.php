<?php

abstract class BaseDouane extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'Douane';
    }
    public function save($doc) {
        
    }
}
