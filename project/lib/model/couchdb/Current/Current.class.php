<?php

class Current extends BaseCurrent {

    public function isDSEditable() {

        return (CurrentClient::getCurrent()->ds_non_editable == 0 && CurrentClient::getCurrent()->ds_non_ouverte == 0);
    }

}