<?php

abstract class BaseRecoltantSiege extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'Recoltant';
       $this->_tree_class_name = 'RecoltantSiege';
    }
    public function save($doc) {
        
    }
}
