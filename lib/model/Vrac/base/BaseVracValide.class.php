<?php
/**
 * BaseVracValide
 * 
 * Base model for VracValide

 * @property string $date_saisie
 * @property string $date_validation_vendeur
 * @property string $date_validation_acheteur
 * @property string $date_validation_mandataire
 * @property string $date_validation
 * @property string $statut

 * @method string getDateSaisie()
 * @method string setDateSaisie()
 * @method string getDateValidationVendeur()
 * @method string setDateValidationVendeur()
 * @method string getDateValidationAcheteur()
 * @method string setDateValidationAcheteur()
 * @method string getDateValidationMandataire()
 * @method string setDateValidationMandataire()
 * @method string getDateValidation()
 * @method string setDateValidation()
 * @method string getStatut()
 * @method string setStatut()
 
 */

abstract class BaseVracValide extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracValide';
    }
                
}