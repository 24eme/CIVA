<?php

class ConfigurationAppellation extends BaseConfigurationAppellation {

    public function getLieux() {
        return $this->filter('^lieu');
    }

    public function hasManyLieu() {
        return (!$this->exist('lieu') || $this->filter('^lieu[0-9]')->count() > 0);
    }

    public function hasLieuEditable() {
        if ($this->exist('lieu_editable') && $this->get('lieu_editable'))
            return true;
        return false;
    }

}
