<?php

class DRAcheteurs extends BaseDRAcheteurs {

    public function addAppellationTypeCVI($appellation, $type, $cvi) {
        $tab = $this->getArrayTypeWithAppellation($type);
        if (isset($tab[$cvi][$appellation]) && $tab[$cvi][$appellation]) {

            return true;
        }

        return ($this->getNoeudAppellations()->add($appellation)->add($type)->add(null,$cvi));
    }

    public function getNoeudAppellations() {

        return $this->add('certification')->add('genre');
    }

    public function getArrayTypeWithAppellation($type) {
        $tab = array();
        foreach($this->getNoeudAppellations() as $appellation_key => $acheteurs) {
            foreach($acheteurs->{$type} as $acheteur_cvi) {
            	$tab[$acheteur_cvi][$appellation_key] = true;
            }
        }

        return $tab;
    }

    public function getArrayType($type) {
        return array_keys($this->getArrayTypeWithAppellation($type));
    }

    public function getTheoriticalNegoces() {
        $negoces = ListAcheteursConfig::getNegoces();
        foreach($this->getArrayNegoces() as $cvi) {
            if (!isset($negoces[$cvi])) {
                $etb = EtablissementClient::getInstance()->findByCvi($cvi);
                if (!$etb) {
                    continue;
                }
                $negoces[$cvi] = array('cvi' => $cvi, 'commune' => $etb->commune, 'nom' => $etb->raison_sociale);
            }
        }
        return $negoces;
    }

    public function getArrayNegoces() {

        return $this->getArrayType('negoces');
    }

    public function getArrayNegocesWithAppellation() {

        return $this->getArrayTypeWithAppellation('negoces');
    }

    public function getArrayCooperatives() {

        return $this->getArrayType('cooperatives');
    }

    public function getArrayCooperativesWithAppellation() {

        return $this->getArrayTypeWithAppellation('cooperatives');
    }

    public function getArrayMouts() {
        $mouts = array();
        foreach($this->getNoeudAppellations() as $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[] = $acheteur_cvi;
            }
        }

        return $mouts;
    }

    public function getArrayMoutsWithAppellation() {
        $mouts = array();
        foreach($this->getNoeudAppellations() as $appellation_key =>  $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[$acheteur_cvi][$appellation_key] = true;
            }
        }

        return $mouts;
    }

    public function getArrayCaveParticuliereWithAppellation() {
        $cave_particuliere = array();
        foreach($this->getNoeudAppellations() as $appellation_key => $acheteurs) {
            $cave_particuliere[$appellation_key] = ($acheteurs->cave_particuliere == 1);
        }

        return $cave_particuliere;
    }
}
