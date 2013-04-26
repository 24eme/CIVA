<?php
/**
 * BaseDSRecolte
 * 
 * Base model for DSRecolte


 
 */

abstract class BaseDSRecolte extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSRecolte';
    }
                
}