<?php

function getEtape3Label($etape,$many_lieux,$dss,$ds) 
{
    if(!$many_lieux) return "";
    if($etape != 3) return  '('.count($dss).' lieux)';
   $posDs = positionEditionDS($dss,$ds);
  return  '(lieu '.$posDs. '/'.count($dss).')';

}

function progressionEdition($dss,$ds,$etape) 
{
    $nb_etapes = 5 + count($dss);
//    $e = 0;
//    if($etape < 3) $e = $etape/$nb_etapes;
//    if($etape > 3) $e = ($etape-1+count($dss))/$nb_etapes;
    $e = $etape/$nb_etapes;
    return (int) ($e * 100);
}

function positionEditionDS($dss,$ds){
    $posDs = 1;
    foreach (array_keys($dss) as $ds_key) {
        if($ds->_id == $ds_key) break;
        $posDs++;
    }
    return $posDs;
}

function getDefaultTotal($type,$appellation,$current_lieu){
    if(($current_lieu) || ($current_lieu->exist($type))) return $appellation->{$type} - $current_lieu->{$type};
    return 0;   
}

function getTitleLieuStockageStock($ds){
    return 'Lieu de stockage : '.$ds->stockage->numero.' - '.$ds->stockage->nom.', '.$ds->stockage->adresse. ", ".$ds->stockage->code_postal." ".$ds->stockage->commune;
}

function isEtapePasse($etape,$dss,$ds){
    if($etape < 3 && $etape < $ds->num_etape)
        return true;
    if($etape >= 3 && ((count($dss) + $etape -1) < $ds->num_etape))
        return true;
    return false;
}