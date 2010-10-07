<?php

abstract class BaseConfigurationLieu extends ConfigurationAbstract {
    public function configureTree() {
       $this->_root_class_name = 'Configuration';
       $this->_tree_class_name = 'ConfigurationLieu';
    }
}
