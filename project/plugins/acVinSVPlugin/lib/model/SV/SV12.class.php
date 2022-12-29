<?php

class SV12 extends SV
{
    protected $colonnes = [
        'superficie', 'recolte', 'revendique', 'mouts', 'superficie_mouts'
    ];

    public function getSum()
    {
        return array_reduce($this->getRecapProduits(), function ($sum, $p) {
            $sum['superficie'] += $p->superficie_recolte;
            $sum['recolte'] += $p->quantite_recolte;
            $sum['revendique'] += $p->volume_revendique;
            $sum['mouts'] += $p->volume_mouts;
            $sum['superficie_mouts'] += $p->superficie_mouts;

            return $sum;
        }, array_fill_keys($this->colonnes, 0));
    }
}
