<?php

abstract class BaseTiersSiege extends acCouchdbDocumentTree {
    public function configureTree() {
       $this->_root_class_name = 'Tiers';
       $this->_tree_class_name = 'TiersSiege';
    }
}
