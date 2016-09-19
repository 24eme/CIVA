<?php
/**
 * Model for DSGenre
 *
 */

class DSGenre extends BaseDSGenre {

    public function getCertification() {

        return $this->getParent();
    }

    public function getChildrenNode() {

        return $this->getAppellations();
    }

    public function getAppellations() {

        return $this->filter('^appellation');
    }

    public function getAppellationsSorted() {
        $appellations = array();
        foreach($this->getConfig()->getArrayAppellations() as $item) {
            $hash = str_replace("recolte", "declaration", HashMapper::inverse($item->getHash()));
            if($this->getDocument()->exist($hash)) {
                $appellations[$hash] = $this->getDocument()->get($hash);
            }
        }

        return $appellations;
    }

    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getCertification()->updateVolumes($vtsgn,$old_volume,$volume);
    }

}
