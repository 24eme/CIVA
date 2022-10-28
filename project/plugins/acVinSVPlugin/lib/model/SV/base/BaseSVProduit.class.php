<?php
/**
 * BaseSVProduit
 * 
 * Base model for SVProduit

 * @property string $libelle
 * @property string $denomination_complementaire
 * @property string $superficie_recolte
 * @property string $quantite_recolte
 * @property string $volume_recolte
 * @property string $volume_revendique

 * @method string getLibelle()
 * @method string setLibelle()
 * @method string getDenominationComplementaire()
 * @method string setDenominationComplementaire()
 * @method string getSuperficieRecolte()
 * @method string setSuperficieRecolte()
 * @method string getQuantiteRecolte()
 * @method string setQuantiteRecolte()
 * @method string getVolumeRecolte()
 * @method string setVolumeRecolte()
 * @method string getVolumeRevendique()
 * @method string setVolumeRevendique()
 
 */

abstract class BaseSVProduit extends acCouchdbDocumentTree {
                
    public function configureTree() {
       $this->_root_class_name = 'SV';
       $this->_tree_class_name = 'SVProduit';
    }
                
}