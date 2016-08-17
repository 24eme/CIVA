<?php
/**
 * Model for DSCertification
 *
 */

class DSCertification extends BaseDSCertification {

    public function getChildrenNode() {

        return $this->getGenres();
    }

    public function getGenres(){

        return $this->filter('^genre');
    }

    public function getAppellationsSorted() {
        $appellations = array();
        foreach($this->getConfig()->getChildrenNode() as $genre) {
            $appellations = array_merge($appellations, $genre->getChildrenNode());
        }

        return $appellations;
    }
}
