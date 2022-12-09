<?php

class SV11 extends SV
{
    protected $colonnes = [
        'superficie', 'recolte', 'revendique', 'usages_industriels', 'vci'
    ];

    public function getSum()
    {
        return array_reduce($this->getRecapProduits(), function ($sum, $p) {
            $sum['superficie'] += $p->superficie_recolte;
            $sum['recolte'] += $p->volume_recolte;
            $sum['revendique'] += $p->volume_revendique;
            $sum['usages_industriels'] += $p->usages_industriels;
            $sum['vci'] += $p->vci;

            return $sum;
        }, array_fill_keys($this->colonnes, 0));
    }
}
