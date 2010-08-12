<?php

class DRRecolteAppellationLieu extends BaseDRRecolteAppellationLieu {

    protected $_total_acheteurs_by_cvi = array();

    public function getLibelle() {
        return ConfigurationClient::getConfiguration()->get($this->getHash())->getLibelle();
    }

    public function getVolumeAcheteur($cvi, $type) {
        $sum = 0;
        foreach ($this->getAcheteursFromCepage($type) as $a) {
            if ($a->cvi == $cvi)
                $sum += $a->quantite_vendue;
        }
        return $sum;
    }

    private function getAcheteursFromCepage($type = 'negoces|cooperatives') {
        $acheteurs = array();
        foreach ($this->filter('^cepage') as $key => $cepage) {
            foreach ($cepage->detail as $key => $d) {
                foreach ($d->filter($type) as $key => $t) {
                    foreach ($t as $key => $a) {
                        array_push($acheteurs, $a);
                    }
                }
            }
        }
        return $acheteurs;
    }

    public function getTotalAcheteursByCvi($field) {
        if (!isset($this->_total_acheteurs_by_cvi[$field])) {
            $this->_total_acheteurs_by_cvi[$field] = array();
            foreach ($this->filter('^cepage') as $object) {
                $acheteurs = $object->getTotalAcheteursByCvi($field);
                foreach ($acheteurs as $cvi => $quantite_vendue) {
                    if (!isset($this->_total_acheteurs_by_cvi[$field][$cvi])) {
                        $this->_total_acheteurs_by_cvi[$field][$cvi] = 0;
                    }
                    $this->_total_acheteurs_by_cvi[$field][$cvi] += $quantite_vendue;
                }
            }
        }
        return $this->_total_acheteurs_by_cvi[$field];
    }

    public function update() {
        parent::update();
        $type = array('negoces' => 'negociant', 'cooperatives' => 'cooperative');
        foreach ($this->getAcheteursFromCepage() as $a) {
            $acheteur = $this->add('acheteurs')->add($a->cvi);
            $acheteur->type_acheteur = $type[$a->getParent()->getKey()];
        }
    }

}
