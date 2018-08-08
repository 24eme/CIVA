<?php
/**
 * Model for DSDeclaration
 *
 */

class DSDeclaration extends BaseDSDeclaration {

    public function getChildrenNode() {

        return $this->getCertifications();
    }

    public function getCertifications() {

        return $this->filter('^certification');
    }

    public function getAppellations() {
        if(!$this->exist('certification/genre')) return array();
        return $this->certification->genre->getAppellations();
    }

    public function getAppellationsSorted() {
        $appellations = array();
        foreach(DSCivaClient::getInstance()->getConfigAppellations($this->getDocument()->getConfig()) as $item) {
            $hash = str_replace("recolte", "declaration", HashMapper::inverse($item->getHash()));
            if($this->getDocument()->exist($hash)) {
                $appellations[$hash] = $this->getDocument()->get($hash);
            }
        }

        return $appellations;
    }

    public function restoreNodes(){
        foreach ($this->getAppellations() as $appellation) {
            if($appellation->isAutoCepages()){
                $appellation->getDocument()->addNoeud($appellation->getHash());
            }
        }
    }

    public function hasGrdCru() {
        return in_array('/declaration/certification/genre/appellation_GRDCRU',array_keys($this->getAppellationsSorted()));
    }

    public function getGrdCru() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_GRDCRU'];
    }

     public function hasCremant() {
        return in_array('/declaration/certification/genre/appellation_CREMANT',array_keys($this->getAppellationsSorted()));
    }

    public function getCremant() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_CREMANT'];
    }

    public function hasAlsaceBlanc() {
        return in_array('/declaration/certification/genre/appellation_ALSACEBLANC',array_keys($this->getAppellationsSorted()));
    }

    public function getAlsaceBlanc() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_ALSACEBLANC'];
    }

    public function hasCommunale() {
        return in_array('/declaration/certification/genre/appellation_COMMUNALE',array_keys($this->getAppellationsSorted()));
    }

    public function getCommunale() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_COMMUNALE'];
    }

     public function hasPinotNoirRouge() {
        return in_array('/declaration/certification/genre/appellation_PINOTNOIRROUGE',array_keys($this->getAppellationsSorted()));
    }

    public function getPinotNoirRouge() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_PINOTNOIRROUGE'];
    }

    public function hasLieuDit() {
        return in_array('/declaration/certification/genre/appellation_LIEUDIT',array_keys($this->getAppellationsSorted()));
    }

    public function getLieuDit() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_LIEUDIT'];
    }

    public function hasPinotNoir() {
        return in_array('/declaration/certification/genre/appellation_PINOTNOIR',array_keys($this->getAppellationsSorted()));
    }

    public function getPinotNoir() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_PINOTNOIR'];
    }

    public function hasVinTable() {
        return in_array('/declaration/certification/genre/appellation_VINTABLE',array_keys($this->getAppellationsSorted()));
    }

    public function getVinTable() {
        $appellationsSorted = $this->getAppellationsSorted();
        return $appellationsSorted['/declaration/certification/genre/appellation_VINTABLE'];
    }


}
