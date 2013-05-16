<?php

/**
 * Model for DSDetail
 *
 */
class DSDetail extends BaseDSDetail {

    public function getCepage() {
        
        return $this->getParent()->getParent();
    }

    public function getProduitHash() {

        return $this->getParent()->getParent()->getHash();
    }

    public function getLibelle() {

        return $this->cepage;
    }

    public function updateVolume($vtsgn, $volume) {
        $old_volume = 0;
        switch ($vtsgn) {
            case DSCivaClient::VOLUME_NORMAL:
                $old_volume = ($this->volume_normal)? $this->volume_normal : 0; 
                $this->volume_normal = $volume;
                break;
            case DSCivaClient::VOLUME_VT:
                $old_volume = ($this->volume_vt)? $this->volume_vt : 0; 
                $this->volume_vt = $volume;
                break;
            case DSCivaClient::VOLUME_SGN:
                $old_volume = ($this->volume_sgn)? $this->volume_sgn : 0; 
                $this->volume_sgn = $volume;
                break;
        }
        $this->getCepage()->getCouleur()->updateVolumes($vtsgn,$old_volume, $volume);
    }

}