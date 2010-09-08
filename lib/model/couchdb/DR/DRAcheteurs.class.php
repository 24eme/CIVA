<?php

class DRAcheteurs extends BaseDRAcheteurs {

    public function getArrayNegoces() {
        $negoces = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->negoces as $acheteur_cvi) {
                $negoces[] = $acheteur_cvi;
            }
        }
        return $negoces;
    }

    public function getArrayNegocesWithAppellation() {
        $negoces = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->negoces as $acheteur_cvi) {
                $negoces[$acheteur_cvi][$appellation] = true;
            }
        }
        return $negoces;
    }

    public function getArrayCooperatives() {
        $cooperatives = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->cooperatives as $acheteur_cvi) {
                $cooperatives[] = $acheteur_cvi;
            }
        }
        return $cooperatives;
    }

    public function getArrayCooperativesWithAppellation() {
        $cooperatives = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->cooperatives as $acheteur_cvi) {
                $cooperatives[$acheteur_cvi][$appellation] = true;
            }
        }
        return $cooperatives;
    }

    public function getArrayMouts() {
        $mouts = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[] = $acheteur_cvi;
            }
        }
        return $mouts;
    }

    public function getArrayMoutsWithAppellation() {
        $mouts = array();
        foreach($this as $appellation => $acheteurs) {
            foreach($acheteurs->mouts as $acheteur_cvi) {
                $mouts[$acheteur_cvi][$appellation] = true;
            }
        }
        return $mouts;
    }

    public function getArrayCaveParticuliereWithAppellation() {
        $cave_particuliere = array();
        foreach($this as $appellation => $acheteurs) {
            $cave_particuliere[$appellation] = ($acheteurs->cave_particuliere == 1);
        }
        return $cave_particuliere;
    }
}
