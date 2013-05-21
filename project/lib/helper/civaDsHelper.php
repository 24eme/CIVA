<?php

function getEtape3Label($many_lieux,$dss,$ds) 
{
    if(!$many_lieux) return "";
   $posDs = positionEditionDS($dss,$ds);
  return  '(lieu '.$posDs. '/'.count($dss).')';

}

function progressionEdition($dss,$ds,$etape) 
{
    $posDs = positionEditionDS($dss,$ds);
    $nb_etapes = 4 + count($dss);
    $e = 0;
    if($etape < 3) $e = $etape/$nb_etapes;
    if($etape > 3) $e = ($etape-1+count($dss))/$nb_etapes;
    $e = ($etape-1+$posDs)/$nb_etapes;
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
    
    if(!$current_lieu) return $appellation->{$type};
    return $appellation->{$type} - $current_lieu->{$type};
    
}