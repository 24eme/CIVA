<?php

/**
 * Model for DSDetail
 *
 */
class DSDetail extends BaseDSDetail {

    public function getCepage() {
        return $this->getParent();
    }

    public function getProduitHash() {

        return $this->getParent()->getParent()->getHash();
    }

    public function getLibelle() {

        return $this->cepage;
    }
    
    public function getCaca() {
        
        return $this->_get('lieu');
    }

    public function updateVolume($vtsgn, $volume) {
        switch ($vtsgn) {
            case DSCivaClient::VOLUME_NORMAL:
                $this->volume_normal = $volume;
                return;
            case DSCivaClient::VOLUME_VT:
                $this->volume_vt = $volume;
                return;
            case DSCivaClient::VOLUME_SGN:
                $this->volume_sgn = $volume;
                return;
        }
    }

}