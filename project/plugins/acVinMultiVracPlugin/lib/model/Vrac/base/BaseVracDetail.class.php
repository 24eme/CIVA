<?php
/**
 * BaseVracDetail
 * 
 * Base model for VracDetail

 * @property string $vtsgn
 * @property string $millesime
 * @property float $prix_unitaire
 * @property string $denomination
 * @property string $solde
 * @property acCouchdbJson $retiraisons

 * @method string getVtsgn()
 * @method string setVtsgn()
 * @method string getMillesime()
 * @method string setMillesime()
 * @method float getPrixUnitaire()
 * @method float setPrixUnitaire()
 * @method string getDenomination()
 * @method string setDenomination()
 * @method string getSolde()
 * @method string setSolde()
 * @method acCouchdbJson getRetiraisons()
 * @method acCouchdbJson setRetiraisons()
 
 */

abstract class BaseVracDetail extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracDetail';
    }
                
}