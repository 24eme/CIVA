<?php

abstract class BaseDSDeclarant extends acCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSDeclarant';
    }
}
