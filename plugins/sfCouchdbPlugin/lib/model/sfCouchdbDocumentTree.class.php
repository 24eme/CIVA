<?php

abstract class sfCouchdbDocumentTree extends sfCouchdbJson {
   protected $_root_class_name = null;
   protected $_tree_class_name = null;


   public function  __construct($definition = null) {
        $this->configureTree();
        parent::__construct($definition);
   }

   public function configureTree() {
       throw new sfCouchdbException("configureTree is not implemented");
   }

   public function setupDefinition() {
       $this->_definition = sfCouchdbManager::getDefinitionTree($this->getRootClassName(), $this->getTreeClassName());
       if (is_null($this->_definition)) {
           throw new sfCouchdbException('Class definition not find');
       }
    }

   public function getRootClassName() {
       if (!class_exists($this->_root_class_name)) {
            echo $this->_root_class_name;
            throw new sfCouchdbException("Root class name don't exist");
       } else {
           return $this->_root_class_name;
       }
   }

   public function getTreeClassName() {
       if (!class_exists($this->_tree_class_name)) {
            throw new sfCouchdbException("Tree class name don't exist");
       } else {
           return $this->_tree_class_name;
       }
   }
}

?>