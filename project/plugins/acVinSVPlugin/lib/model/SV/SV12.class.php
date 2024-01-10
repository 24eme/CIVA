<?php

class SV12 extends SV
{
    protected $colonnes = [
        'superficie', 'recolte', 'revendique', 'mouts', 'superficie_mouts', 'revendique_mouts'
    ];

    public function getSum()
    {
        return array_reduce($this->getRecapProduits(), function ($sum, $p) {
            $sum['superficie'] += $p->superficie_totale;
            $sum['recolte'] += $p->quantite_recolte;
            $sum['revendique'] += $p->volume_revendique;
            $sum['mouts'] += $p->volume_mouts;
            $sum['superficie_mouts'] += $p->superficie_mouts;
            $sum['revendique_mouts'] += $p->volume_mouts_revendique;

            return $sum;
        }, array_fill_keys($this->colonnes, 0));
    }

    public function hasMouts()
    {
        $total = $this->getSum();

        return $total['superficie_mouts'] > 0 || $total['mouts'] > 0;
    }
}
