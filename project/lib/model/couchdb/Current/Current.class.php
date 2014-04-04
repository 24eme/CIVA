<?php

class Current extends BaseCurrent {

    public function isDREditable() {
        return (CurrentClient::getCurrent()->dr_non_editable == 0 && CurrentClient::getCurrent()->dr_non_ouverte == 0);
    }

    public function isDSEditable() {

        return (CurrentClient::getCurrent()->ds_non_editable == 0 && CurrentClient::getCurrent()->ds_non_ouverte == 0);
    }

    public function getCampagneDS() {
    	$campagne_manager = new CivaCampagneManager('08-01');    	
    	return $campagne_manager->getCurrent();
    }
    
    public function getPeriodeDS()
    {
        if(!$this->exist('ds_periode') && $this->ds_periode){
            return null;
        }
        return $this->ds_periode;
    }

    public function getAnneeDS() {

        return preg_replace("/^[0-9]+-/", "", $this->getCampagneDS());
    }

}