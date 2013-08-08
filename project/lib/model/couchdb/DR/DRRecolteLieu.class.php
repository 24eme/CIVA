<?php

class DRRecolteLieu extends BaseDRRecolteLieu {

    const USAGES_INDUSTRIELS_NOEUD_LIEU = 'lieu';
    const USAGES_INDUSTRIELS_NOEUD_DETAIL = 'detail';

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

    public function getVolumeRevendique($force_calcul = false) {

        return parent::getDataByFieldAndMethod('volume_revendique', array($this,'getVolumeRevendiqueFinal'), $force_calcul);
    }

    public function getVolumeRevendiqueWithoutUIS() {

        return $this->getVolumeRevendiqueFinal();
    }


    public function getVolumeRevendiqueTotal() {

        return parent::getDataByFieldAndMethod("volume_revendique_total", array($this,"getSumNoeudFields"), true, array('volume_revendique'));
    }

    public function getVolumeRevendiqueTotalWithUIS() {
        $volume_revendique = round($this->getVolumeRevendiqueTotal(), 2);
        if($volume_revendique < round($this->getTotalVolume(), 2)) {

            return $volume_revendique;
        }

        return $volume_revendique - $this->usages_industriels_saisi;
    }
    public function getVolumeRevendiqueRendement() {

        return $this->getVolumeRevendiqueAppellation();
    }

    public function getVolumeRevendiqueAppellation() {
        $key = "volume_revendique_appellation";
        if (!isset($this->_storage[$key])) {
            $volume_revendique = 0;
            if ($this->getConfig()->hasRendement() && $this->getConfig()->hasRendementAppellation()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxAppellation();
                if ($volume > $volume_max) {
                    $volume_revendique = $volume_max;
                } else {
                    $volume_revendique = $volume;
                }
            }
            $this->_storage[$key] = $volume_revendique;
        }

        return $this->_storage[$key];
    }

    public function getVolumeRevendiqueAppellationWithUIS() {
        $volume_revendique = $this->getVolumeRevendiqueAppellation();
        if($volume_revendique < $this->getTotalVolume()) {

            return $volume_revendique;
        }

        return $volume_revendique - $this->usages_industriels_saisi;
    }

    public function getDplc($force_calcul = false) {
        return parent::getDataByFieldAndMethod('dplc', array($this, 'getDplcFinal'), $force_calcul);
    }

    /*public function getUsageIndustrielCalcule($force_calcul = false) {
        $dplc = $this->getDplc($force_calcul);
        if($dplc > 0) {

            return $dplc;
        }

        return ($this->usages_industriels_saisi) ? $this->usages_industriels_saisi : 0;
    }*/

    public function getDplcTotal() {

        return parent::getDataByFieldAndMethod('dplc_total', array($this, 'getSumNoeudFields'),true, array('dplc'));
    }

    public function getUsageIndustrielCalculeTotal($force_calcul = false) {
        $dplc = $this->getDplcTotal();
        if($dplc > 0) {

            return $dplc;
        }

        return $this->usages_industriels_saisi;
    }

    public function getUsageIndustrielSaisi() {
        if($this->getTotalCaveParticuliere() == 0) {
            return 0;
        }
        return $this->_get('usages_industriels_saisi');
    }

    public function getDplcRendement() {

        return $this->getDplcAppellation();
    }

    public function getDplcAppellation() {
        $key = "dplc_appellation";
        if (!isset($this->_storage[$key])) {
            $volume_dplc = 0;
            if ($this->getConfig()->hasRendementAppellation()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxAppellation();
                if ($volume > $volume_max) {
                    $volume_dplc = $volume - $volume_max;
                } else {
                    $volume_dplc = 0;
                }
            }
            $this->_storage[$key] = round($volume_dplc, 2);
        }
        return $this->_storage[$key];
    }

    public function getUsagesIndustriels($force_calcul = false) {

        return parent::getDataByFieldAndMethod('usages_industriels_calcule', array($this, 'getUsagesIndustrielsFinal'), $force_calcul);
    }

    public function getUsagesIndustrielsFinal() {
        if($this->usages_industriels_noeud == self::USAGES_INDUSTRIELS_NOEUD_DETAIL) {

            return $this->getUsagesIndustrielsTotal();
        }

        if($this->usages_industriels_noeud == self::USAGES_INDUSTRIELS_NOEUD_LIEU) {

            return $this->usages_industriels_saisi;
        }

        return $this->getDplc();
    }

    public function getUsagesIndustrielsTotal() {

        return $this->getSumNoeudFields('usages_industriels', false);
    }

    public function getUsageIndustrielCalculeAppellation($force_calcul = false) {
        $dplc = $this->getDplcAppellation();
        if($dplc > 0) {

            return $dplc;
        }

        return $this->usages_industriels_saisi;
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

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
            return round($this->getTotalVolume() / ($this->getTotalSuperficie() / 100), 0);
        } else {
            return 0;
        }
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
        if (!$this->getConfig()->hasRendement() || !$this->hasAcheteurs()) {
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
        if (!$this->getConfig()->hasRendement()) {
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

    public function getVolumeMaxAppellation() {
        return round(($this->getTotalSuperficie() / 100) * $this->getConfig()->getRendementAppellation(), 2);
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

    public function getDplcFinal() {
        $dplc_total = $this->getDplcTotal();
        $dplc_final = $dplc_total;
        if ($this->getConfig()->hasRendement() && $this->getConfig()->hasRendementAppellation()) {
            $dplc_appellation = $this->getDplcAppellation();
            if ($dplc_total < $dplc_appellation) {
                $dplc_final = $dplc_appellation;
            }
        }
        return $dplc_final;
    }

    public function getVolumeRevendiqueFinal() {
        
        return round($this->getTotalVolume() - $this->getUsagesIndustriels(), 2);
    }

    public function haveUsagesIndustrielsSaisi() {

        return !is_null($this->usages_industriels_noeud);
    }

    public function haveUsagesIndustrielsSaisiInDetails() {
        foreach($this->getProduitsDetails() as $detail) {
            if(!is_null($detail->usages_industriels_saisi)) {
                return true;
            }
        }

        return false;
    }

    public function canHaveUsagesIndustrielsSaisi() {
        if(!$this->usages_industriels_noeud) {

            return true;
        }

        return $this->usages_industriels_noeud == DRRecolteLieu::USAGES_INDUSTRIELS_NOEUD_LIEU;
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
            /* $this->total_volume = $this->getTotalVolume(true);
              $this->total_superficie = $this->getTotalSuperficie(true); */
            if($this->usages_industriels_noeud != DRRecolteLieu::USAGES_INDUSTRIELS_NOEUD_LIEU && !is_null($this->usages_industriels_saisi)) {
                $this->usages_industriels_noeud = DRRecolteLieu::USAGES_INDUSTRIELS_NOEUD_LIEU;
            } elseif($this->usages_industriels_noeud == DRRecolteLieu::USAGES_INDUSTRIELS_NOEUD_LIEU) {
                $this->usages_industriels_noeud = null;
            }

            $this->dplc = $this->getDplc(true);
            $this->usages_industriels_calcule = $this->getUsagesIndustriels(true);
            $this->volume_revendique = $this->getVolumeRevendique(true);
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

        if ($this->getCouchdbDocument()->canUpdate() /*&& $this->getConfig()->hasRendement()*/ && $this->hasSellToUniqueAcheteur()) {
            $unique_acheteur->superficie = $this->getTotalSuperficie();
            $unique_acheteur->dontdplc = $this->getDplc();
        }
    }

}
