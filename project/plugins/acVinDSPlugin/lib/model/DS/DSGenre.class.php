<?php
/**
 * Model for DSGenre
 *
 */

class DSGenre extends BaseDSGenre {

    public function getCertification() {

        return $this->getParent();
    }
    public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $itemsconfigParent = $this->getConfig()->getParent();
        $items_sorted = array();
        foreach($itemsconfigParent as $items_config) {
            foreach($items_config->getChildrenNode() as $hashConfig => $item_config) {
                $hashDS = str_replace("recolte", "declaration", HashMapper::inverse($item_config->getHash()));
                if($this->getDocument()->exist($hashDS)) {
                    $item = $this->getDocument()->get($hashDS);
                    $items_sorted[$item->getHash()] = $item;
                }
            }
        }

        return $items_sorted;
    }

    public function getChildrenNode() {

        return $this->getAppellations();
    }

    public function getAppellations() {

        return $this->filter('^appellation');
    }

    public function hasManyLieu() {

        return false;
    }

    /*public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $items_config = $this->getDocument()->declaration->getConfig()->getArrayAppellations();
        $items_sorted = array();

        foreach($items_config as $hashConfig => $item_config) {
            $hashDS = str_replace("recolte", "declaration", HashMapper::inverse($item_config->getHash()));
            if($this->getDocument()->exist($hashDS)) {
                $item = $this->getDocument()->get($hashDS);
                $items_sorted[$item->getHash()] = $item;
            }
        }

        return $items_sorted;
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
    }*/

    public function updateVolumes($vtsgn,$old_volume,$volume) {
        parent::updateVolumes($vtsgn, $old_volume, $volume);
        $this->getCertification()->updateVolumes($vtsgn,$old_volume,$volume);
    }

}
