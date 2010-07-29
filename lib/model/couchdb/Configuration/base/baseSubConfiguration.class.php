<?php

abstract class BaseSubConfiguration extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'SubConfiguration';
    }
    public function save($doc) {
        
    }
}
