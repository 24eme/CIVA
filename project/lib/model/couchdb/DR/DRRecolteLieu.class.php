<?php

class DRRecolteLieu extends BaseDRRecolteLieu {

    public function getLibelleWithAppellation() {
        if ($this->getLibelle())
            return $this->getParent()->getParent()->getLibelle() . ' '.$this->getParent()->getLibelle() . ' - ' . $this->getLibelle();
        return $this->getParent()->getParent()->getLibelle().' '.$this->getParent()->getLibelle();
    }

    public function getMention() {

        return $this->getParent();
    }

    public function getAppellation() {

        return $this->getMention()->getAppellation();
    }

    public function getLieu() {

        return $this;
    }

    public function getChildrenNode() {

        return $this->getCouleurs();
    }

    public function hasRecapitulatif() {

        return !$this->getConfig()->existRendementCouleur() && $this->getConfig()->existRendement();
    }

    public function getCouleurs() {
        return $this->filter('^couleur');
    }

    public function getCouleur($cepage = null) {
        if (!$cepage && count($this->getConfig()->couleurs) > 1)
            throw new sfException("getCouleur() ne peut être appelé d'un lieu qui n'a qu'une seule couleur...");
        $couleur = 'couleur';
        if ($cepage)
            foreach ($this->getCouleurs() as $couleur => $obj) {
                if ($obj->exist($cepage))
                    break;
            }
        return $this->_get($couleur);
    }

    public function hasCepageRB() {

        return $this->getCepageRB() !== null;
    }

    public function getCepageRB() {

        $cepage_rebeche = array();
        foreach ($this->filter('couleur') as $couleur)
            if( $couleur->exist('cepage_RB'))
                $cepage_rebeche[] = $couleur->get('cepage_RB');

        if( count($cepage_rebeche) > 1)
            throw new sfException("getCepagesRB() ne peut retourner plus d'un cepage rebeche par appellation");

        return (count($cepage_rebeche) == 1) ? $cepage_rebeche[0] : null;
    }

    public function getNbCouleurs() {

        return count($this->filter('^couleur'));
    }

    public function getCepages() {
        throw new sfException("La liste des cépages est impossible à partir du lieu");

        return $this->getCouleur()->getCepages();
    }

    public function getCodeDouane($vtsgn = '') {
        if ($this->getParent()->getKey() == 'appellation_VINTABLE') {
            if ($this->getParent()->getParent()->filter('appellation_')->count() > 1) {
                $vtsgn = 'AOC';
            }
        }
        if ($this->getAppellation()->getConfig()->hasManyLieu()) {

            return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
        } else {

            return $this->getAppellation()->getConfig()->getDouane()->getFullAppCode($vtsgn);
        }
    }

    public function isInManyMention(){

        $arr_lieux = array();
        foreach( $this->getAppellation()->getMentions() as $mention){
            if($mention->filter($this)){
                $arr_lieux[] = $this;
            }
        }
        return (count($arr_lieux) > 1) ? true : false;
    }

    public function canHaveUsagesLiesSaisi() {

        return !$this->isLiesSaisisCepage() && !$this->getConfig()->existRendementCouleur();
    }

    protected function update($params = array()) {
        $this->preUpdateAcheteurs();
        parent::update($params);

        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->dplc = $this->getDplc(true);
            $this->lies = $this->getLies(true);
            $this->usages_industriels = $this->getUsagesIndustriels(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
        }

        $this->updateAcheteurs();
    }

}
