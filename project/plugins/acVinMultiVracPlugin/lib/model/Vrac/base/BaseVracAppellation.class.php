<?php
/**
 * BaseVracAppellation
 * 
 * Base model for VracAppellation

 * @property string $libelle
 * @property string $libelle_long
 * @property VracMention $mention

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getLibelleLong()
 * @method string setLibelleLong()
 * @method VracMention getMention()
 * @method VracMention setMention()
 
 */

abstract class BaseVracAppellation extends _VracNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracAppellation';
    }
                
}