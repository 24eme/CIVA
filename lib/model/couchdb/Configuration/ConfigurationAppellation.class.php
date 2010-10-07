<?php

class ConfigurationAppellation extends BaseConfigurationAppellation {
    public function getLieux() {
        return $this->filter('^lieu');
    }

    public function hasManyLieu() {
        return (!$this->exist('lieu') || $this->filter('^lieu[0-9]')->count() > 0);
    }
}
