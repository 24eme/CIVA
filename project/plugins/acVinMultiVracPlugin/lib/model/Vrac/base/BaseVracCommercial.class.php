<?php
/**
 * BaseVracCommercial
 * 
 * Base model for VracCommercial

 * @property string $nom
 * @property string $email

 * @method string getNom()
 * @method string setNom()
 * @method string getEmail()
 * @method string setEmail()
 
 */

abstract class BaseVracCommercial extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracCommercial';
    }
                
}