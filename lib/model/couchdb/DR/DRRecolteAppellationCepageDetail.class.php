<?php

class DRRecolteAppellationCepageDetail extends BaseDRRecolteAppellationCepageDetail {
    public function getAcheteursValuesWithCvi($field) {
        $acheteurs = $this->get($field);
        $values = array();
        foreach($acheteurs as $acheteur) {
            $values[$acheteur->cvi] = $acheteur->quantite_vendue;
        }
        return $values;
    }
}
