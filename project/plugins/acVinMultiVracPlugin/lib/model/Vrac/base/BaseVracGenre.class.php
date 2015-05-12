<?php
/**
 * BaseVracGenre
 * 
 * Base model for VracGenre


 
 */

abstract class BaseVracGenre extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracGenre';
    }
                
}