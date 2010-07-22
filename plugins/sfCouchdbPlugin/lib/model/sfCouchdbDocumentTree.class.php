<?php

abstract class sfCouchdbDocumentTree extends sfCouchdbJson {
   protected $_root_class_name = null;
   protected $_tree_class_name = null;

   public function  __construct($definition_model = null, $definition_hash = null) {
        $this->configureTree();
        parent::__construct($definition_model, $definition_hash);
   }

   public function configureTree() {
       throw new sfCouchdbException("configureTree is not implemented");
   }

   public function setupDefinition() {
       $this->_definition_model = call_user_func_array(array($this->getRootClassName(), 'getDocumentDefinitionModel'), array());
       $this->_definition_hash = sfCouchdbManager::getDefinitionHashTree($this->getRootClassName(), $this->getTreeClassName());
       if (is_null($this->_definition_hash)) {
           throw new sfCouchdbException('definition hash not find');
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