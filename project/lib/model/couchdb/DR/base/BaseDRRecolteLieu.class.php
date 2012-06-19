<?php

abstract class BaseDRRecolteLieu extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'DR';
       $this->_tree_class_name = 'DRRecolteLieu';
    }
}
