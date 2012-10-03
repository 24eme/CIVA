<?php

class ConfigurationGenre extends BaseConfigurationGenre {

    public function getAppellations() {

        return $this->filter('^appellation');
    }

    public function getMentions() {

        return $this->getNoeudsSuivant();
    }

    public function getNoeuds() {

        return $this->getAppellations();
    }

    public function hasManyAppellations() {

        return $this->getNoeudsSuivant()->hasManyNoeuds();
    }


}
