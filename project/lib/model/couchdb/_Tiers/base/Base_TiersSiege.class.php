<?php

abstract class Base_TiersSiege extends sfCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = '_Tiers';
       $this->_tree_class_name = '_TiersSiege';
    }
}
