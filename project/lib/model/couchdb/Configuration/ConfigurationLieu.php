<?php

class ConfigurationLieu extends BaseConfigurationLieu {

    public function getMention() {

        return $this->getParentNode();
    }

    public function getAppellation() {

        return $this->getMention()->getParentNode();
    }

    public function getCouleurs() {
        return $this->filter('^couleur');
    }

    public function getChildrenNode() {

        return $this->getCouleurs();
    }

    public function getCouleur() {
        if ($this->getNbCouleurs() > 1) {
            throw new sfException('Pas getCouleur si plusieurs couleurs');
        }
        return $this->_get('couleur');
    }

    public function getNbCouleurs() {

        return count($this->getCouleurs());
    }

    public function getCepagesFilter($type_declaration = null) {
        $cepages = array();
        foreach($this->getChildrenFilter($type_declaration) as $couleur) {
            $cepages = array_merge($cepages, $couleur->getChildrenFilter($type_declaration));
        }

        return $cepages;
    }

    public function getCepages() {
        $cepage = array();
        foreach ($this->getCouleurs() as $couleur) {
            $cepage = array_merge($cepage, $couleur->getCepages()->toArray());
        }
        return $cepage;
    }

    public function hasManyCouleur() {
        return (!$this->exist('couleur') || $this->filter('^couleur.+')->count() > 0);
    }
    
    public function hasLieuEditable(){

        return $this->getAppellation()->hasLieuEditable();
    }

    public function getRendementNoeud() {

        return $this->getRendementAppellation();
    }

}
