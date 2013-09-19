<?php

class DRRecolteCouleur extends BaseDRRecolteCouleur {

    public function getLibelleWithAppellation() {
        if ($this->getLibelle())
	        return $this->getAppellation()->getLibelle().' - '.$this->getLibelle();

        return $this->getAppellation()->getLibelle();
    }

    public function getLieu() {

        return $this->getParent();
    }

    public function getAppellation() {

        return $this->getLieu()->getAppellation();
    }

    public function getChildrenNode() {

        return $this->getCepages();
    }

    public function getCepages() {

        return $this->filter('^cepage');
    }

    public function canHaveUsagesLiesSaisi() {

        return !$this->isLiesSaisisCepage();
    }

    public function getAutreCouleur() {
        $couleurs = $this->getParent()->getCouleurs();
        foreach($couleurs as $couleur) {
            if ($couleur->getKey() != $this->getKey()) {
                return $couleur;
            }
        }
        throw new sfException("Pas d'autre couleur");
    }

    public function getCodeDouane($vtsgn = '') {
        if ($this->getLieu()->getConfig()->hasManyCouleur()) {
            return $this->getConfig()->getDouane()->getFullAppCode($vtsgn);
        } else {
            return $this->getLieu()->getCodeDouane($vtsgn);
        }
    }

    protected function update($params = array()) {
        parent::update($params);
        
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_volume = $this->getTotalVolume(true);
            $this->total_superficie = $this->getTotalSuperficie(true);
            $this->dplc = $this->getDplc(true);
            $this->lies = $this->getLies(true);
            $this->usages_industriels = $this->getUsagesIndustriels(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
        }

        $this->updateAcheteurs();

        $this->cleanNoeuds();
    }
    
    protected function cleanNoeuds() {
        foreach($this->getCepages() as $cepage) {
            if (count($cepage->detail) == 0) {
                $this->remove($cepage->getKey());
            }
        }
    }
}
