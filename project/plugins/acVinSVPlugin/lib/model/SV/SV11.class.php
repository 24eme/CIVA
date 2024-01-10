<?php

class SV11 extends SV
{
    protected $colonnes = [
        'superficie', 'recolte', 'revendique', 'volume_detruit', 'vci'
    ];

    public function getSum()
    {
        return array_reduce($this->getRecapProduits(), function ($sum, $p) {
            $sum['superficie'] += $p->superficie_totale;
            $sum['recolte'] += $p->volume_recolte;
            $sum['revendique'] += $p->volume_revendique;
            $sum['volume_detruit'] += $p->volume_detruit;
            $sum['vci'] += $p->vci;

            return $sum;
        }, array_fill_keys($this->colonnes, 0));
    }
}
