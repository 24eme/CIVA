<?php
/**
 * BaseDSCertification
 * 
 * Base model for DSCertification

 * @property float $total_volume
 * @property float $total_superficie
 * @property float $volume_revendique
 * @property float $dplc
 * @property float $usages_industriels_calcule

 * @method float getTotalVolume()
 * @method float setTotalVolume()
 * @method float getTotalSuperficie()
 * @method float setTotalSuperficie()
 * @method float getVolumeRevendique()
 * @method float setVolumeRevendique()
 * @method float getDplc()
 * @method float setDplc()
 * @method float getUsagesIndustrielsCalcule()
 * @method float setUsagesIndustrielsCalcule()
 
 */

abstract class BaseDSCertification extends _DSNoeud {
                
    public function configureTree() {
       $this->_root_class_name = 'DS';
       $this->_tree_class_name = 'DSCertification';
    }
                
}