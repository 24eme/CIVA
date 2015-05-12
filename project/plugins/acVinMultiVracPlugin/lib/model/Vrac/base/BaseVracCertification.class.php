<?php
/**
 * BaseVracCertification
 * 
 * Base model for VracCertification


 
 */

abstract class BaseVracCertification extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracCertification';
    }
                
}