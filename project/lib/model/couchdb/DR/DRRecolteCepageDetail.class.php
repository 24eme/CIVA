<?php

class DRRecolteCepageDetail extends BaseDRRecolteCepageDetail {

    public function getConfig() {
        return $this->getCepage()->getConfig();
    }

    public function getCepageLibelle() {
      return $this->getCepage()->getLibelle();
    }

    public function getCepage() {
      return $this->getParent()->getParent();
    }

    public function getCouleur() {
        return $this->getCepage()->getCouleur();
    }

    public function getLieuNode() {
        return $this->getCouleur()->getLieu();
    }

    public function getMention() {
        return $this->getLieuNode()->getMention();
    }

    public function getCodeDouane() {

        return $this->getCepage()->getCodeDouane($this->vtsgn);
    }

    public function getTotalVolume() {

        return $this->volume;
    }

    public function getTotalSuperficie() {

        return $this->superficie;
    }

    public function getDplc() {

        return $this->volume_dplc;
    }

    public function getRevendique() {

        return $this->volume_revendique;
    }

    public function getTotalCaveParticuliere() {

        return $this->cave_particuliere;
    }

    public function getTotalVci() {

        return $this->vci;
    }

    public function getLiesMax() {

        return round($this->cave_particuliere + $this->getVolumeAcheteurs('mouts'), 2);
    }

    public function getTotalDontDplcVendus() {

        return null;
    }

    public function getTotalDontVciVendusByCvi($type, $cvi) {
        if(!$this->getTotalVci()) {

            return 0;
        }
        if($this->getVolumeVenduByCvi($type, $cvi) == $this->volume) {

            return $this->getTotalVci();
        }

        $recap = $this->getCepage()->getNoeudRecapitulatif();

        if($recap->getTotalDontVciVendusByCvi($type, $cvi) == $recap->getTotalVci()) {

            return $this->getTotalVci();
        }

        if(!$recap->getTotalDontVciVendusByCvi($type, $cvi)) {

            return 0;
        }

        return null;
    }

    public function getVolumeVenduByCvi($type, $cvi) {
        $volume = 0;
        foreach($this->get($type) as $achat) {
            if($achat->cvi != $cvi) {
                continue;
            }

            $volume += $achat->quantite_vendue;
        }

        return $volume;
    }

    public function getVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
            $this->_storage[$key] = array();
            foreach ($this->filter($type) as $acheteurs) {
                foreach($acheteurs as $acheteur) {
                    if(!array_key_exists($acheteur->cvi, $this->_storage[$key])) {
                        $this->_storage[$key][$acheteur->cvi] = 0;
                    }

                    $this->_storage[$key][$acheteur->cvi] += $acheteur->quantite_vendue;
                }
            }
        }
        return $this->_storage[$key];
    }

    public function getTotalVolumeAcheteurs($type = 'negoces|cooperatives|mouts') {
        $key = "total_volume_acheteurs_".$type;
        if (!isset($this->_storage[$key])) {
              $sum = 0;
              $acheteurs = $this->getVolumeAcheteurs($type);
              foreach($acheteurs as $volume) {
                $sum += $volume;
              }
              $this->_storage[$key] = $sum;
        }
        return $this->_storage[$key];
    }

    public function getVolumeByAcheteur($cvi) {
       if(!$this->exist('negoces')){
           return 0;
       }
       foreach ($this->negoces as $negoce) {
           if($negoce->cvi == $cvi){
               return $negoce->quantite_vendue;
           }
       }
       return 0;
    }

    public function getTotalVciAcheteur($type) {
        $vci = 0;
        if(!$this->getTotalVci()) {
            return 0;
        }
        foreach($this->get($type) as $achat) {
            $volume = $this->getTotalDontVciVendusByCvi($type, $achat->cvi);
            if(is_null($volume)) {
                return null;
            }
            $vci += $volume;
        }

        return $vci;
    }

    protected function deleteAcheteurUnused($type) {
        $appellation_key = $this->getCepage()->getLieu()->getAppellation()->getKey();
        if($this->getCepage()->getLieu()->getMention()->getKey() != "mention") {
            $appellation_key = $this->getCepage()->getLieu()->getMention()->getKey();
        }
        if ($this->exist($type) && $this->getCouchdbDocument()->acheteurs->getNoeudAppellations()->exist($appellation_key) &&           $this->getCouchdbDocument()->acheteurs->getNoeudAppellations()->get($appellation_key)) {
            $acheteurs = $this->getCouchdbDocument()->acheteurs->getNoeudAppellations()->get($appellation_key)->get($type);
            $acheteurs_detail = $this->get($type);
            foreach ($acheteurs_detail as $key => $item) {
                if (!in_array($item->cvi, $acheteurs->toArray())) {
                    $acheteurs_detail->remove($key);
                }
            }
        }
    }

    public function getVolumeMax() {

        return round(($this->superficie / 100) * $this->getConfig()->getRendementNoeud(), 2);
    }

    public function setVolume($v) {
        return $this->_set('volume', round($v, 2));
    }

    private function getSumAcheteur($field) {
        $sum = 0;
        if ($this->exist($field)) {
            foreach ($this->get($field) as $acheteur) {
                $sum += $acheteur->quantite_vendue;
            }
        }
        return $sum;
    }

    public function removeVolumes() {
        $this->setVolume(null);
        $this->cave_particuliere = null;
        $this->lies = null;
        if($this->exist('vci')) {
            $this->vci = null;
        }
        $this->remove('cooperatives');
        $this->remove('mouts');
        $this->remove('negoces');
        if($this->exist('motif_non_recolte') && $this->motif_non_recolte != 'AE') {
            $this->remove('motif_non_recolte');
        }
    }

    public function hasMotifNonRecolteLibelle() {
        return $this->exist('motif_non_recolte');
    }

    public function isNonSaisie() {
        return ($this->getMotifNonRecolteLibelle() == 'Déclaration en cours');
    }

    public function getMotifNonRecolteLibelle() {
        if ($this->volume)
            return '';

        $motifs_non_recolte = sfConfig::get('app_configuration_dr_motifs_non_recolte');

        if ($this->exist('motif_non_recolte') && isset($motifs_non_recolte[$this->motif_non_recolte])) {
            return $motifs_non_recolte[$this->motif_non_recolte];
        } else {
            return 'Déclaration en cours';
        }
    }

    public static function getUKey( $lieu ="", $denom, $vtsgn) {

         return 'lieu:'.strtolower($lieu).',denomination:'.strtolower($denom).',vtsgn:'.strtolower($vtsgn);
    }

    public function getUniqueKey() {
        return self::getUKey($this->lieu, $this->denomination, $this->vtsgn);
    }

    public function getVtsgn() {
        return str_replace(' ', '', $this->_get('vtsgn'));
    }

    protected function update($params = array()) {
        parent::update($params);
        if (!$this->getCouchdbDocument()->canUpdate())
            return;
        $v = $this->cave_particuliere;
        $v += $this->getSumAcheteur('negoces');
        $v += $this->getSumAcheteur('cooperatives');
        $v += $this->getSumAcheteur('mouts');

        $this->volume = $v;
        $this->volume_dplc = null;
        $this->lies = $this->getLies(true);

        $this->usages_industriels = $this->lies;
        $this->volume_revendique = $this->volume - ($this->usages_industriels - $this->getLiesMouts());

        if ($this->volume && $this->volume > 0) {
            $this->remove('motif_non_recolte');
        } else {
            $this->add('motif_non_recolte');
        }
        if (in_array('from_acheteurs', $params)) {
            $this->deleteAcheteurUnused('negoces');
            $this->deleteAcheteurUnused('cooperatives');
            $this->deleteAcheteurUnused('mouts');
        }
    }

    public function getLiesMouts() {
        $volume_mouts = $this->getTotalVolumeAcheteurs('mouts');

        if(!$volume_mouts) {

            return 0;
        }

        if($this->cave_particuliere > 0) {

            return 0;
        }

        return $this->lies;
    }

    public function canHaveUsagesLiesSaisi() {

        return $this->getCepage()->isLiesSaisisCepage();
    }

    public function canHaveVci() {

        return $this->getCepage()->canHaveVci();
    }

    public function getLies($force = false) {
        if(!$force) {

            return $this->_get('lies');
        }

        if(!$this->canHaveUsagesLiesSaisi()) {

            return 0;
        }


        return $this->lies;
    }

    public function cleanLies() {
        $this->lies = null;
    }

    public function canCalculVolumeRevendiqueSurPlace() {
        return true;
    }

    public function canCalculSuperficieSurPlace() {
        return true;
    }

    public function canCalculInfosVente() {
        $nbDestinataire = 0;
        if($this->cave_particuliere > 0) {
            $nbDestinataire++;
        }

        foreach($this->getVolumeAcheteurs() as $cvi => $volumeVendu) {
            if($volumeVendu > 0) {
                $nbDestinataire++;
            }
        }

        return $nbDestinataire == 1;
    }
}
