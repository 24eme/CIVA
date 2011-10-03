<?php

class ConfigurationAppellation extends BaseConfigurationAppellation {

    public function getLieux() {
        return $this->filter('^lieu');
    }

    public function hasManyLieu() {
        return (!$this->exist('lieu') || $this->filter('^lieu.+')->count() > 0);
    }

    public function hasLieuEditable() {
        if ($this->exist('detail_lieu_editable') && $this->get('detail_lieu_editable'))
            return true;
        return false;
    }

}
