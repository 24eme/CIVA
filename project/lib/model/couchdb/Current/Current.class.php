<?php

class Current extends BaseCurrent {

    public function isDSEditable() {

        return (CurrentClient::getCurrent()->ds_non_editable == 0 && CurrentClient::getCurrent()->ds_non_ouverte == 0);
    }

    public function getCampagneDS() {
    	$campagne_manager = new CivaCampagneManager('08-01');
    	
    	return (string) ($campagne_manager->getCurrent() - 1);
    }

}