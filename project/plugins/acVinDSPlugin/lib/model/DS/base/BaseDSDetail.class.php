<?php
/**
 * BaseDSDetail
 *
 * Base model for DSDetail

 * @property string $vtsgn
 * @property float $volume_vt
 * @property float $volume_sgn
 * @property float $volume_normal
 * @property string $lieu
 * @property float $stock_declare
 * @property float $vci
 * @property float $reserve_qualitative

 * @method string getVtsgn()
 * @method string setVtsgn()
 * @method float getVolumeVt()
 * @method float setVolumeVt()
 * @method float getVolumeSgn()
 * @method float setVolumeSgn()
 * @method float getVolumeNormal()
 * @method float setVolumeNormal()
 * @method string getLieu()
 * @method string setLieu()
 * @method float getStockDeclare()
 * @method float setStockDeclare()
 * @method float getVci()
 * @method float setVci()
 * @method float getReserveQualitative()
 * @method float setReserveQualitative()
 */

abstract class BaseDSDetail extends acCouchdbDocumentTree {

    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSDetail';
    }

}
