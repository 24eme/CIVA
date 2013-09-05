<?php

class DRRecolteLieu extends BaseDRRecolteLieu {

    public function getLibelleWithAppellation() {
        if ($this->getLibelle())
            return $this->getParent()->getParent()->getLibelle() . ' - ' . $this->getLibelle();
        return $this->getParent()->getParent()->getLibelle();
    }

    public function getMention() {

        return $this->getParent();
    }

    public function getAppellation() {

        return $this->getMention()->getAppellation();
    }

    public function getChildrenNode() {

        return $this->getCouleurs();
    }

    public function getCouleurs() {
        return $this->filter('^couleur');
    }

    public function getCouleur($cepage = null) {
        if (!$cepage && $this->getConfig()->getNbCouleurs() > 1)
            throw new sfException("getCouleur() ne peut être appelé d'un lieu qui n'a qu'une seule couleur...");
        $couleur = 'couleur';
        if ($cepage)
            foreach ($this->getCouleurs() as $couleur => $obj) {
                if ($obj->exist($cepage))
                    break;
            }
        return $this->_get($couleur);
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

    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_" . $type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach ($this->getCouleurs() as $couleur) {
                foreach ($couleur->getCepages() as $cepage) {
                    if ($cepage->getConfig()->excludeTotal()) {
                        continue;
                    }
                    $acheteurs = $cepage->getVolumeAcheteurs($type);
                    foreach ($acheteurs as $cvi => $quantite_vendue) {
                        if (!isset($this->_storage[$key][$cvi])) {
                            $this->_storage[$key][$cvi] = 0;
                        }
                        $this->_storage[$key][$cvi] += $quantite_vendue;
                    }
                }
            }
        }
        return $this->_storage[$key];
    }

    public function getVolumeAcheteur($cvi, $type) {
        $volume = 0;
        $acheteurs = $this->getVolumeAcheteurs($type);
        if (array_key_exists($cvi, $acheteurs)) {
            $volume = $acheteurs[$cvi];
        }
        return $volume;
    }

    public function getTotalVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "total_volume_acheteurs_" . $type;
        if (!isset($this->_storage[$key])) {
            $sum = 0;
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $volume) {
                $sum += $volume;
            }
            $this->_storage[$key] = $sum;
        }
        
        return $this->_storage[$key];
    }

    public function getTotalVolumeForMinQuantite() {
        
        return round($this->getTotalVolume() - $this->getTotalVolumeAcheteurs('negoces'), 2);
    }

    public function removeVolumes() {
        $this->volume_revendique = null;
        $this->total_volume = null;
        $this->dplc = null;
        foreach ($this->getCouleurs() as $couleur) {
            foreach ($couleur->getCepages() as $cepage) {
                $cepage->removeVolumes();
            }
        }
    }

    public function hasSellToUniqueAcheteur() {
        if ($this->getTotalCaveParticuliere() > 0) {
            return false;
        }
        $vol_total_cvi = array();
        $acheteurs = array();
        $types = array('negoces', 'cooperatives', 'mouts');
        foreach ($types as $type) {
            foreach ($this->getVolumeAcheteurs($type) as $cvi => $volume) {
                if (!isset($vol_total_cvi[$type . '_' . $cvi])) {
                    $vol_total_cvi[$type . '_' . $cvi] = 0;
                }
                $vol_total_cvi[$type . '_' . $cvi] += $volume;
            }
        }
        if (count($vol_total_cvi) != 1) {
            return false;
        }
        return true;
    }

    public function hasCompleteRecapitulatifVente() {
        if (!$this->getConfig()->existRendement() || !$this->hasAcheteurs()) {
            return true;
        }

        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->superficie) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getTotalSuperficieRecapitulatifVente() {
        $total_superficie = 0;
        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->superficie) {
                    $total_superficie += $acheteur->superficie;
                }
            }
        }

        return $total_superficie;
    }

    public function getTotalDontDplcRecapitulatifVente() {
        $total_dontdplc = 0;
        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->dontdplc) {
                    $total_dontdplc += $acheteur->dontdplc;
                }
            }
        }

        return $total_dontdplc;
    }

    public function isValidRecapitulatifVente() {
        if (!$this->getConfig()->existRendement()) {
            return true;
        }
        return (round($this->getTotalSuperficie(), 2) >= round($this->getTotalSuperficieRecapitulatifVente(), 2) &&
                round($this->getDplc(), 2) >= round($this->getTotalDontDplcRecapitulatifVente(), 2));
    }

    public function hasAcheteurs() {
        $nb_acheteurs = 0;
        foreach ($this->acheteurs as $type => $type_acheteurs) {
            $nb_acheteurs += $type_acheteurs->count();
        }

        return $nb_acheteurs > 0;
    }

    public function isNonSaisie() {
        $cpt = 0;
        foreach ($this->getCouleurs() as $couleur) {
            foreach ($couleur->getCepages() as $key => $cepage) {
                $cpt++;
                if (!$cepage->isNonSaisie())
                    return false;
            }
        }
        return ($cpt);
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

        if ($this->getCouchdbDocument()->canUpdate()) {
            $total_superficie_before = $this->getTotalSuperficie();
            $total_volume_before = $this->getTotalVolume();
            unset($this->_storage['total_superficie']);
            unset($this->_storage['total_volume']);
        }

        parent::update($params);
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->dplc = $this->getDplc(true);
            $this->usages_industriels = $this->getUsagesIndustriels(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
            $this->lies = $this->getLies(true);
        }

        $this->add('acheteurs');
        $types = array('negoces', 'cooperatives', 'mouts');
        $unique_acheteur = null;
        foreach ($types as $type) {
            $acheteurs = $this->getVolumeAcheteurs($type);
            foreach ($acheteurs as $cvi => $volume) {
                $acheteur = $this->acheteurs->add($type)->add($cvi);
                $acheteur->type_acheteur = $type;
                $unique_acheteur = $acheteur;
                if ($this->getCouchdbDocument()->canUpdate() && (round($this->getTotalSuperficie(), 2) != round($total_superficie_before, 2) ||
                                                                 round($this->getTotalVolume(), 2) != round($total_volume_before, 2))) {
                    $acheteur->superficie = null;
                    $acheteur->dontdplc = null;
                }
            }
            $acheteurs_to_remove = array();
            foreach ($this->acheteurs->get($type) as $cvi => $item) {
                if (!array_key_exists($cvi, $acheteurs)) {
                    $acheteurs_to_remove[] = $type."/".$cvi;
                    //$this->acheteurs->get($type)->remove($cvi);
                }
            }

            foreach($acheteurs_to_remove as $hash) {
                $this->acheteurs->remove($hash);
            }
        }
        $this->acheteurs->update();

        if ($this->getCouchdbDocument()->canUpdate() && $this->hasSellToUniqueAcheteur()) {
            $unique_acheteur->superficie = $this->getTotalSuperficie();
            $unique_acheteur->dontdplc = $this->getDplc();
        }
    }

}
