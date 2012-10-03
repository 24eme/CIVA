<?php

abstract class BaseDRRecolteCertification extends _DRRecolteNoeud {
    public function configureTree() {
       $this->_root_class_name = 'DR';
       $this->_tree_class_name = 'DRRecolteCertification';
    }
}
