<?php

class ConfigurationMention extends BaseConfigurationMention {

    public function hasManyLieu() {
            if( count($this->filter('^lieu')) > 1 )
                return true;
        return false;
    }

    public function getLieux(){
        return $this->filter('^lieu');
    }
}
