<?php

abstract class sfCouchdbDocumentTree extends sfCouchdbJson {
   protected $_root_class_name = null;
   protected $_tree_class_name = null;


   public function  __construct() {
        $this->configureTree();
        parent::__construct();
   }

   public function configureTree() {
       throw new sfCouchdbException("configureTree is not implemented");
   }

   public function setupDefinition() {
       $root_data = call_user_func_array(array($this->getRootClassName(), 'getRootDefinition'), array());
       $definition_data = sfCouchdbJsonDefinitionParser::searchDefinitionByClass($root_data, $this->getTreeClassName());
       if ($definition_data) {
          $this->_definition = sfCouchdbJsonDefinitionParser::parse($definition_data);
       } else {
           parent::setupDefinition();
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