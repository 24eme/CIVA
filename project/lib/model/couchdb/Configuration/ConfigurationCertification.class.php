<?php

class ConfigurationCertification extends BaseConfigurationCertification {

    public function getGenres() {

        return $this->filter('^genre');
    }

    public function getAppellations() {

        return $this->getNoeudsSuivant();
    }

    public function getNoeud() {
        return $this->genre;
    }

    public function getNoeuds() {

        return $this->getGenres();
    }

    public function hasManyGenre() {

        return $this->getNoeudsSuivant()->hasManyNoeuds();
    }

}
