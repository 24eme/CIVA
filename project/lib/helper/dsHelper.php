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
    $lieu_num = $ds->getLieuStockage();
    return 'Lieu de stockage : '.$ds->declarant->cvi.$lieu_num.' - '.$ds->getEtablissement()->getNom().', '.$ds->getEtablissement()->getAdresse(). ", ".$ds->getEtablissement()->getCodePostal()." ".$ds->getEtablissement()->getCommune();
}

function isEtapePasse($etape,$dss,$ds){
    if($etape < 3 && $etape < $ds->num_etape)
        return true;
    if($etape >= 3 && ((count($dss) + $etape -1) < $ds->num_etape))
        return true;
    return false;
}