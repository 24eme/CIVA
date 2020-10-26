<?php

abstract class _DRRecolteNoeud extends acCouchdbDocumentTree {

    protected $total_superficie_before;
    protected $total_volume_before;
    protected $total_vci_before;

    public function getConfig() {

        return $this->getDocument()->getConfig()->get(HashMapper::convert($this->getHash()));
    }

    abstract public function getChildrenNode();

    public function getChildrenNodeDeep($level = 1) {
      if($this->getConfig()->hasManyNoeuds()) {

          throw new sfException("getChildrenNodeDeep() peut uniquement être appelé d'un noeud qui contient un seul enfant...");
      }

      $node = $this->getChildrenNode()->getFirst();

      if($level > 1) {

        return $node->getChildrenNodeDeep($level - 1);
      }

      return $node->getChildrenNode();
    }

    public function getChildrenNodeSorted() {
        $items = $this->getChildrenNode();
        $items_config = $this->getConfig()->getChildrenNode();
        $items_sorted = array();

        foreach($items_config as $hash => $item_config) {
            if($this->exist($item_config->getKey())) {
                $items_sorted[$hash] = $this->get($item_config->getKey());
            }
        }

        return $items_sorted;
    }

    public function getProduits() {
        $produits = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $produits = array_merge($produits, $item->getProduits());
        }

        return $produits;
    }

    public function getProduitsDetails() {
        $produits = array();
        foreach($this->getChildrenNode() as $key => $item) {
            $produits = array_merge($produits, $item->getProduitsDetails());
        }
        return $produits;
    }

    public function getRendementRecoltant() {
        if ($this->getTotalSuperficie() > 0) {
            return round(($this->getTotalVolume() - $this->getLiesTotal()) / ($this->getTotalSuperficie() / 100), 4);
        } else {
            return 0;
        }
    }

    public function getRendementMax() {

        return round($this->getConfig()->getRendementNoeud(), 2);
    }

    public function getTotalSuperficie($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_superficie", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalVolume($force_calcul = false) {

        return $this->getDataByFieldAndMethod("total_volume", array($this,"getSumNoeudFields"), $force_calcul);
    }

    public function getTotalCaveParticuliere() {

        return $this->getDataByFieldAndMethod('total_cave_particuliere', array($this, 'getSumNoeudWithMethod'), true, array('getTotalCaveParticuliere') );
    }

    public function getSuperficieCaveParticuliere() {

        return round($this->getTotalSuperficie() - $this->getTotalSuperficieVendus(), 2);
    }

    public function getVolumeRevendiqueCaveParticuliere() {

        return round($this->getTotalCaveParticuliere() - $this->getUsagesIndustrielsCaveParticuliere() - $this->getVciCaveParticuliere(), 2);
    }

    public function getUsagesIndustrielsCaveParticuliere() {
        if(!$this->getTotalCaveParticuliere()) {

            return 0;
        }

        return round($this->getUsagesIndustriels() - $this->getTotalDontDplcVendus(), 2);
    }

    public function getVciCaveParticuliere() {
        if(!$this->getTotalCaveParticuliere()) {

            return 0;
        }

        return round($this->getTotalVci() - $this->getTotalDontVciVendus(), 2);
    }

    public function getUsagesIndustrielsSurPlace() {
        if(!$this->getTotalCaveParticuliere()) {

            return $this->getLiesMouts();
        }

        return $this->getUsagesIndustrielsCaveParticuliere();
    }

    public function getTotalRebeches() {

            return $this->getDataByFieldAndMethod('total_rebeches', array($this, 'getSumNoeudWithMethod'), true, array('getTotalRebeches', false) );
    }

    public function getSurPlaceRebeches() {

        return $this->getDataByFieldAndMethod('rebeches', array($this, 'getSumNoeudWithMethod'), true, array('getSurPlaceRebeches', false) );
    }

    public function getTotalVolumeVendus() {

        return $this->getTotalVolumeAcheteurs();
    }

    public function getLies($force_calcul = false) {
        if(!$this->canHaveUsagesLiesSaisi()) {

            return $this->getDataByFieldAndMethod('lies', array($this, 'getSumNoeudWithMethod'), $force_calcul, array('getLies') );
        }

        return $this->_get('lies') ? $this->_get('lies') : 0;
    }

    public function getLiesMouts() {
        if(!$this->canHaveUsagesLiesSaisi()) {

            return $this->getSumNoeudWithMethod('getLiesMouts');
        }

        $volume_mouts = $this->getTotalVolumeAcheteurs('mouts');

        if(!$volume_mouts) {

            return 0;
        }

        if($this->getTotalCaveParticuliere() > 0) {

            return 0;
        }

        return $this->getLies();
    }

    public function getLiesMax($force_calcul = false) {

        return round($this->getTotalCaveParticuliere($force_calcul) + $this->getTotalVolumeAcheteurs('mouts'), 2);
    }

    public function getDplc($force_calcul = false) {
        if($this->_get('dplc') && !$force_calcul) {

            return $this->_get('dplc');
        }

        if(!$this->getConfig()->hasRendementNoeud()) {

            $dplc = $this->getDataByFieldAndMethod("dplc", array($this,"getDplcTotal") , $force_calcul);
        } else {
            $dplc = $this->getDataByFieldAndMethod('dplc', array($this, 'findDplc'), $force_calcul);
        }

        return $dplc;
    }

    public function getDplcReel($force_calcul = false) {
        $dplcReel = round($this->getDplcWithVci() - $this->getLies(), 2);

        return ($dplcReel > 0) ? $dplcReel : 0;
    }

    public function getDplcCaveParticuliere() {

        return $this->getDplcReel() - $this->getTotalDontDplcVendus();
    }

    public function getDplcWithVci($force_calcul = false) {
        $dplc = $this->getDplc($force_calcul);

        $dplcWithVci = round($dplc - $this->getTotalVci(), 2);

        if($dplcWithVci < 0) {

            return 0;
        }

        return $dplcWithVci;
    }

    public function getDplcTotal() {

        return $this->getDataByFieldAndMethod('dplc_total', array($this, 'getSumNoeudFields'),true, array('dplc'));
    }

    public function getUsagesIndustrielsCalcule() {

        return $this->getUsagesIndustriels();
    }

    public function findDplc() {
        $dplc_total = $this->getDplcTotal();
        $dplc = $dplc_total;
        if ($this->getConfig()->hasRendementNoeud()) {
            $dplc_rendement = $this->getDplcRendement();
            if ($dplc_total < $dplc_rendement) {
                $dplc = $dplc_rendement;
            }
        }
        return $dplc;
    }

    public function getDplcRendement() {
        $key = "dplc_rendement";
        if (!isset($this->_storage[$key])) {
            $volume_dplc = 0;
            if ($this->getConfig()->hasRendementNoeud()) {
                $volume = $this->getTotalVolume();
                $volume_max = $this->getVolumeMaxRendement();
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

    public function getVolumeMaxRendement() {

        return round(($this->getTotalSuperficie() / 100) * $this->getConfig()->getRendementNoeud(), 2);
    }

    public function getVolumeRevendique($force_calcul = false) {
        if($this->_get('volume_revendique') && !$force_calcul) {

            return $this->_get('volume_revendique');
        }

        return $this->getDataByFieldAndMethod('volume_revendique', array($this, 'findVolumeRevendique'), $force_calcul);
    }

    public function getVolumeRevendiqueTotal($force_calcul = false) {

        return $this->getDataByFieldAndMethod('volume_revendique', array($this, 'getSumNoeudFields'), $force_calcul);
    }

    public function findVolumeRevendique() {

        return round(min($this->getVolumeRevendiqueWithDplc(), $this->getVolumeRevendiqueWithUI()), 2);
    }

    public function getVolumeRevendiqueWithDplc() {

        return $this->getTotalVolume() - $this->getDplc();
    }

    public function getVolumeRevendiqueWithUI() {

        return $this->getTotalVolume() - ($this->getUsagesIndustriels() - $this->getLiesMouts());
    }

    public function getUsagesIndustriels($force_calcul = false) {
        if($this->exist('usages_industriels_calcule') && $this->_get('usages_industriels_calcule') && !$force_calcul) {

            return $this->_get('usages_industriels_calcule');
        }

        if($this->_get('usages_industriels') && !$force_calcul) {

            return $this->_get('usages_industriels');
        }

        if(!$this->getConfig()->hasRendementNoeud()) {

            return $this->getUsagesIndustrielsTotal();
        }

        return $this->getDplcWithVci() > $this->getLies() ? $this->getDplcWithVci() : $this->getLies();
    }

    public function getDepassementGlobal() {

        return max($this->getLies(), $this->getDplc());
    }

    public function getUsagesIndustrielsTotal() {

        return $this->getDataByFieldAndMethod('usages_industriels_total', array($this, 'getSumNoeudFields'), true, array('usages_industriels'));
    }

    public function getLiesTotal() {

        return $this->getDataByFieldAndMethod('lies_total', array($this, 'getSumNoeudFields'), true, array('lies'));
    }

    public function canHaveUsagesLiesSaisi() {

        return false;
    }

    protected function getSumNoeudFields($field, $exclude = true) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $key => $noeud) {
            if($exclude && $noeud->getConfig()->excludeTotal()) {

                continue;
            }

            $sum += $noeud->get($field);
        }
        return $sum;
    }

    public function cleanLies() {
        $this->lies = null;

        foreach($this->getChildrenNode() as $item) {
            $item->cleanLies();
        }
    }

    public function isLiesSaisisCepage() {

        return $this->getDocument()->exist('lies_saisis_cepage') && $this->getDocument()->get('lies_saisis_cepage');
    }

    public function canHaveVci() {
        return $this->getConfig()->canHaveVci();
    }

    public function getTotalVci($force_calcul = true) {
        if(!$force_calcul && $this->exist('vci') && $this->_get('vci')) {

            return $this->_get('vci');
        }

        return $this->getDataByFieldAndMethod('total_vci', array($this, 'getSumNoeudWithMethod'), true, array('getTotalVci'));
    }

    public function getVolumeVciMax() {
        if(!$this->hasRecapitulatif()) {
            return -1;
        }

        return round($this->getRendementVciMax() * $this->getTotalSuperficie() / 100, 2);
    }

    public function getRendementVciMax() {
        if(!$this->getConfigRendementVci() || !$this->hasRecapitulatif()) {

            return 0;
        }
        $rendementExcedent = round($this->getRendementRecoltant() - $this->getConfig()->getRendementNoeud(), 2);

        if($rendementExcedent > $this->getConfigRendementVci()) {

            return $this->getConfigRendementVci();
        }

        if($rendementExcedent < 0) {

            return 0;
        }

        return $rendementExcedent;
    }

    public function getConfigRendementVci() {

        return $this->getConfig()->rendement_vci;
    }

    public function getConfigRendementCepageMinimum() {

        return $this->getRendementNoeud();
    }

    public function getLibelle() {

        return $this->store('libelle', array($this, 'findLibelle'));
    }

    public function removeVolumes() {
        $this->total_volume = null;
        $this->volume_revendique = null;
        $this->dplc = null;
        $this->usages_industriels = null;
        $this->lies = null;
        if($this->exist('vci')) {
            $this->vci = null;
        }

        if($this->exist('usages_industriels_saisi')) {
            $this->remove('usages_industriels_saisi');
        }

        if($this->exist('usages_industriels_calcule')) {
            $this->remove('usages_industriels_calcule');
        }

        foreach ($this->getChildrenNode() as $children) {
            $children->removeVolumes();
        }
    }

    public function isNonSaisie() {
        $details = $this->getProduitsDetails();

        if(count($details) == 0) {

            return false;
        }

        foreach ($this->getProduitsDetails() as $children) {
            if (!$children->isNonSaisie())
                return false;

        }
        return true;
    }


    public function hasRecapitulatif() {

        return false;
    }

    public function getNoeudRecapitulatif() {

        if($this->hasRecapitulatif()) {

            return $this;
        }

        return $this->getParent()->getNoeudRecapitulatif();
    }

    public function canCalculVolumeRevendiqueSurPlace() {
        if($this->getTotalCaveParticuliere() == 0) {

            return true;
        }
        if(!$this->hasCompleteRecapitulatifVenteDplc()) {

            return false;
        }
        if(!$this->hasCompleteRecapitulatifVenteVci()) {

            return false;
        }
        foreach($this->getChildrenNode() as $item) {
            if(!$item->canCalculVolumeRevendiqueSurPlace()) {

                return false;
            }
        }

        return true;
    }

    public function canCalculSuperficieSurPlace() {
        if($this->getTotalCaveParticuliere() == 0) {
            return true;
        }
        if(!$this->hasCompleteRecapitulatifVenteSuperficie()) {

            return false;
        }
        foreach($this->getChildrenNode() as $item) {
            if(!$item->canCalculSuperficieSurPlace()) {

                return false;
            }
        }

        return true;
    }

    /******* Acheteurs *******/

    public function getDontDplcVendusMax() {

        return round($this->getDplc(), 2);
    }

    public function getDontVciVendusMax() {

        return round($this->getTotalVci(), 2);
    }

    public function getTotalDontDplcVendus() {
        if($this->getTotalCaveParticuliere() == 0) {

            return $this->getUsagesIndustriels();
        }

        if(!$this->hasRecapitulatifVente()) {

            return $this->getDataByFieldAndMethod('total_dont_dplc_vendus', array($this, 'getSumNoeudWithMethod'), true, array('getTotalDontDplcVendus'));
        }

        return round($this->getTotalDontDplcRecapitulatifVente(), 2);
    }

    public function getTotalDontVciVendus() {
        if($this->getTotalCaveParticuliere() == 0) {

            return $this->getTotalVci();
        }

        if(!$this->hasRecapitulatifVente()) {

            return $this->getDataByFieldAndMethod('total_dont_vci_vendus', array($this, 'getSumNoeudWithMethod'), true, array('getTotalDontVciVendus'));
        }

        return round($this->getTotalDontVciRecapitulatifVente(), 2);
    }

    public function getTotalDontVciVendusByType($type) {
        if(!$this->hasRecapitulatifVente()) {
            $sum = 0;
            foreach ($this->getChildrenNode() as $noeud) {
                if($noeud->getConfig()->excludeTotal()) {

                    continue;
                }

                $sum += $noeud->getTotalDontVciVendusByType($type);
            }

            return $sum;
        }

        return round($this->getTotalDontVciRecapitulatifVente($type), 2);
    }

    public function getTotalSuperficieVendus() {
        if($this->getTotalCaveParticuliere() == 0) {

            return $this->getTotalSuperficie();
        }

        if(!$this->hasRecapitulatifVente()) {

            return $this->getDataByFieldAndMethod('total_superficie_vendus', array($this, 'getSumNoeudWithMethod'), true, array('getTotalSuperficieVendus'));
        }

        return $this->getTotalSuperficieRecapitulatifVente();
    }

     public function getTotalSuperficieVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatifVente() && $this->acheteurs->exist($type."/".$cvi)) {

            return $this->acheteurs->get($type)->get($cvi)->superficie;
        }

        if($this->hasRecapitulatifVente()) {

            return 0;
        }

        $superficie = null;

        foreach($this->getChildrenNode() as $children) {
            $superficie += $children->getTotalSuperficieVendusByCvi($type, $cvi);
        }

        return $superficie;
    }

    public function getTotalDontDplcVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatifVente() && $this->acheteurs->exist($type."/".$cvi)) {

            return $this->acheteurs->get($type)->get($cvi)->dontdplc;
        }

        if($this->hasRecapitulatifVente()) {

            return 0;
        }

        $dontdplc = null;

        foreach($this->getChildrenNode() as $children) {
            $dontdplc += $children->getTotalDontDplcVendusByCvi($type, $cvi);
        }

        return $dontdplc;
    }

    public function getTotalDontVciVendusByCvi($type, $cvi) {
        if($this->hasRecapitulatifVente() && $this->acheteurs->exist($type."/".$cvi)) {

            return $this->acheteurs->get($type)->get($cvi)->dontvci;
        }

        if($this->hasRecapitulatifVente()) {

            return 0;
        }

        $dontvci = null;

        foreach($this->getChildrenNode() as $children) {
            $dontvci += $children->getTotalDontVciVendusByCvi($type, $cvi);
        }

        return $dontvci;
    }

    public function hasRecapitulatifVente() {

        return $this->exist('acheteurs') && $this->hasRecapitulatif();
    }

    public function hasCompleteRecapitulatifVente() {

        return $this->hasCompleteRecapitulatifVenteDplc() && $this->hasCompleteRecapitulatifVenteSuperficie() && $this->hasCompleteRecapitulatifVenteVci();
    }

    public function hasNoCompleteRecapitulatifVente() {
        if(!$this->hasRecapitulatifVente()) {
            return false;
        }

        if(!$this->hasAcheteurs()) {

            return false;
        }

        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($this->getDplc() > 0 && !is_null($acheteur->dontdplc)) {
                    return false;
                }
                if ($this->getTotalVci() > 0 && !is_null($acheteur->dontvci)) {
                    return false;
                }
                if ($this->getTotalSuperficie() > 0 && !is_null($acheteur->superficie)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasCompleteRecapitulatifVenteDplc() {
        if(!$this->hasRecapitulatifVente()) {
            return true;
        }

        if(!$this->hasAcheteurs()) {

            return true;
        }

        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($this->getDplc() > 0 && is_null($acheteur->dontdplc)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasCompleteRecapitulatifVenteVci() {
        if(!$this->hasRecapitulatifVente()) {
            return true;
        }

        if(!$this->hasAcheteurs()) {

            return true;
        }

        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($this->getTotalVci() > 0 && is_null($acheteur->dontvci)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function hasCompleteRecapitulatifVenteSuperficie() {
        if(!$this->hasRecapitulatifVente()) {
            return true;
        }

        if(!$this->hasAcheteurs()) {

            return true;
        }

        foreach ($this->acheteurs as $type => $type_acheteurs) {
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($this->getTotalSuperficie() > 0 && is_null($acheteur->superficie)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function getTotalSuperficieRecapitulatifVente() {
        if(!$this->hasRecapitulatifVente()) {

            throw new sfException("Ce ne noeud ne permet pas de stocker des acheteurs");
        }

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
        if(!$this->hasRecapitulatifVente()) {

            throw new sfException("Ce ne noeud ne permet pas de stocker des acheteurs");
        }

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

    public function getTotalDontVciRecapitulatifVente($typeFilter = null) {
        if(!$this->hasRecapitulatifVente()) {

            throw new sfException("Ce ne noeud ne permet pas de stocker des acheteurs");
        }

        $total_dontvci = 0;
        foreach ($this->acheteurs as $type => $type_acheteurs) {
            if($typeFilter && $typeFilter != $type) {

                continue;
            }
            foreach ($type_acheteurs as $cvi => $acheteur) {
                if ($acheteur->dontvci) {
                    $total_dontvci += $acheteur->dontvci;
                }
            }
        }

        return $total_dontvci;
    }

    public function isValidRecapitulatifVente() {
        if(!$this->hasRecapitulatifVente()) {
            return true;
        }

        return (round($this->getTotalSuperficie(), 2) >= round($this->getTotalSuperficieRecapitulatifVente(), 2) &&
                round($this->getDplc(), 2) >= round($this->getTotalDontDplcRecapitulatifVente(), 2) &&
                round($this->getTotalVci(), 2) >= round($this->getTotalDontVciRecapitulatifVente(), 2));
    }

    public function hasAcheteurs() {
        if(!$this->exist('acheteurs')) {

            throw new sfException("Ce ne noeud ne permet pas de stocker des acheteurs");
        }

        $nb_acheteurs = 0;
        foreach ($this->acheteurs as $type => $type_acheteurs) {
            $nb_acheteurs += $type_acheteurs->count();
        }

        return $nb_acheteurs > 0;
    }

    public function preUpdateAcheteurs() {
        if ($this->getCouchdbDocument()->canUpdate()) {
            $this->total_superficie_before = $this->getTotalSuperficie();
            $this->total_volume_before = $this->getTotalVolume();
            $this->total_vci_before = $this->getTotalVci(false);
        }
    }

    public function updateAcheteurs() {
        if(!$this->exist('acheteurs')) {

            throw new sfException("Ce ne noeud ne permet pas de stocker des acheteurs");
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
                if ($this->getCouchdbDocument()->canUpdate() && (round($this->getTotalSuperficie(), 2) != round($this->total_superficie_before, 2) || round($this->getTotalVolume(), 2) != round($this->total_volume_before, 2))) {
                    $acheteur->dontdplc = null;
                }

                if ($this->getCouchdbDocument()->canUpdate() && (round($this->getTotalVci(), 2) != round($this->total_vci_before, 2))) {
                    $acheteur->dontvci = null;
                }

                if ($this->getCouchdbDocument()->canUpdate() && (round($this->getTotalSuperficie(), 2) != round($this->total_superficie_before, 2))) {
                    $acheteur->superficie = null;
                }

                if($this->getCouchdbDocument()->canUpdate() && !$this->hasRecapitulatifVente()) {
                    $acheteur->superficie = $this->getTotalSuperficieVendusByCvi($type, $cvi);
                    $acheteur->dontdplc = $this->getTotalDontDplcVendusByCvi($type, $cvi);
                    $acheteur->dontvci = $this->getTotalDontVciVendusByCvi($type, $cvi);
                }
            }
            $acheteurs_to_remove = array();
            foreach ($this->acheteurs->get($type) as $cvi => $item) {
                if (!array_key_exists($cvi, $acheteurs)) {
                    $acheteurs_to_remove[] = $type."/".$cvi;
                }
            }

            foreach($acheteurs_to_remove as $hash) {
                $this->acheteurs->remove($hash);
            }
        }
        $this->acheteurs->update();

        if ($this->getCouchdbDocument()->canUpdate() && $this->hasRecapitulatifVente() && $this->hasSellToUniqueAcheteur()) {
            $unique_acheteur->superficie = $this->getTotalSuperficie();
            $unique_acheteur->dontdplc = $this->getDplc();
            $unique_acheteur->dontvci = $this->getTotalVci();
        }
    }

    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts', $excludeTotal = true) {
        $key = "volume_acheteurs_" . $type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach($this->getChildrenNode() as $children) {
                if ($excludeTotal && $children->getConfig()->excludeTotal()) {
                    continue;
                }
                $acheteurs = $children->getVolumeAcheteurs($type);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
                        if (!isset($this->_storage[$key][$cvi])) {
                            $this->_storage[$key][$cvi] = 0;
                        }
                        if ($quantite_vendue) {
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

    public function getTotalCaveParticuliereForMinQuantite() {

        return round($this->getTotalCaveParticuliere() + $this->getTotalVolumeAcheteurs('mouts'), 2);
    }

    public function getVolumeAcheteursForMinQuantite() {

        return $this->getVolumeAcheteurs('cooperatives');
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

    public function getAcheteursArray() {
        $acheteurs = array();
        $types = array('negoces', 'cooperatives', 'mouts');
        foreach ($types as $type) {
            foreach ($this->getVolumeAcheteurs($type) as $cvi => $volume) {
                if (!isset($acheteurs[$type . '_' . $cvi])) {
                    $acheteurs[$type . '_' . $cvi] = 0;
                }
                $acheteurs[$type . '_' . $cvi] += $volume;
            }
        }
        return $acheteurs;
    }

    /******* Fin Acheteurs *******/

    protected function findLibelle() {

        return $this->getConfig()->getLibelle();
    }

    protected function getSumFields($field) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $k => $noeud) {
            $sum += $noeud->get($field);
        }
        return $sum;
    }

    protected function issetField($field) {
        return ($this->_get($field) || $this->_get($field) === 0);
    }

    protected function getDataByFieldAndMethod($field, $method, $force_calcul = false, $parameters = array()) {
        if (!$force_calcul && $this->issetField($field)) {
            return $this->_get($field);
        }

        if(!empty($parameters)){
            return $this->store($field, $method, $parameters);
        }

        return $this->store($field, $method, array($field));
    }

    protected function getSumNoeudWithMethod($method, $exclude = true) {
        $sum = 0;
        foreach ($this->getChildrenNode() as $noeud) {
            if($exclude && $noeud->getConfig()->excludeTotal()) {

                continue;
            }

            $sum += $noeud->$method();
        }
        return $sum;
    }

    public function cleanAllNodes() {
        $keys_to_delete = array();
        foreach($this->getChildrenNode() as $item) {
            $item->cleanAllNodes();
            if(!count($item->getProduitsDetails())){
                $keys_to_delete[$item->getKey()] = $item->getKey();
            }
        }

        foreach($keys_to_delete as $key) {
            $this->remove($key);
        }
    }

    public function getMentions() {
        $mentions = array();

        foreach($this->getChildrenNode() as $item) {
            foreach($item->getMentions() as $mention) {
                $mentions[$mention->getHash()] = $mention;
            }
        }

        return $mentions;
    }

}
