<?php

class Current extends BaseCurrent {

    public function isDREditable() {
        return (CurrentClient::getCurrent()->dr_non_editable == 0 && CurrentClient::getCurrent()->dr_non_ouverte == 0);
    }

    public function isDSEditable() {

        return (CurrentClient::getCurrent()->ds_non_editable == 0 && CurrentClient::getCurrent()->ds_non_ouverte == 0);
    }

    public function getCampagneDS() {

        return (int) (preg_replace("/^([0-9]{4})[0-9]{2}$/", '\1', $this->getPeriodeDS()) - 1);
    }
    
    public function getPeriodeDS()
    {
        if(!$this->exist('ds_periode') && $this->ds_periode){
            return null;
        }
        return $this->ds_periode;
    }

    public function isDSDecembre() {

        return $this->getMonthDS() == "12";
    }

    public function getMonthDS() {

        return substr($this->ds_periode, 4, 2);
    }

    public function getAnneeDS() {

        return $this->getCampagneDS() + 1;
    }

}