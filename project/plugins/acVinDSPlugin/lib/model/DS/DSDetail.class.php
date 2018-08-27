<?php

/**
 * Model for DSDetail
 *
 */
class DSDetail extends BaseDSDetail {

    public function getCepage() {

        return $this->getParent()->getParent();
    }

    public function getConfig() {

        return $this->getCepage()->getConfig();
    }

    public function getProduitHash() {

        return $this->getParent()->getParent()->getHash();
    }

    public function getLibelle() {
        if(!$this->cepage->getLibelle()) {
            return $this->cepage->getAppellation()->getLibelle();
        }
        return $this->cepage->getLibelle();
    }

    public function updateVolume($vtsgn, $volume, $sum = false) {
        $old_volume = 0;
        switch ($vtsgn) {
            case DSCivaClient::VOLUME_NORMAL:
                $old_volume = ($this->volume_normal) ? $this->volume_normal : 0;
                if ($sum) {
                    $this->volume_normal += $volume;
                    $volume = $this->volume_normal;
                } else {
                    $this->volume_normal = $volume;
                }
                break;
            case DSCivaClient::VOLUME_VT:
                $old_volume = ($this->volume_vt) ? $this->volume_vt : 0;
                if ($sum) {
                    $this->volume_vt += $volume;
                    $volume = $this->volume_vt;
                } else {
                    $this->volume_vt = $volume;
                }
                break;
            case DSCivaClient::VOLUME_SGN:
                $old_volume = ($this->volume_sgn) ? $this->volume_sgn : 0;
                if ($sum) {
                    $this->volume_sgn += $volume;
                    $volume = $this->volume_sgn;
                } else {
                    $this->volume_sgn = $volume;
                }
                break;
        }
        $this->getCepage()->getCouleur()->updateVolumes($vtsgn, $old_volume, $volume);
    }

    public function isSaisi() {
        
       return !is_null($this->volume_normal) || !is_null($this->volume_vt) || !is_null($this->volume_sgn);
    }


    public function isVide() {

       return !$this->volume_normal && !$this->volume_vt && !$this->volume_sgn;
    }

}