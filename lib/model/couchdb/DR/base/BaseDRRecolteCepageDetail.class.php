<?php

abstract class BaseDRRecolteCepageDetail extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'DR';
       $this->_tree_class_name = 'DRRecolteCepageDetail';
    }
}
