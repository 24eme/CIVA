<?php

abstract class BaseConfigurationAppellation extends ConfigurationAbstract {
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationAppellation';
    }
}
