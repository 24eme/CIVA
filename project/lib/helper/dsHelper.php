<?php

function getEtape3Label($etape,$many_lieux,$dss,$ds) 
{
    if(!$many_lieux) return "";
    if($etape != 3) return  '('.count($dss).' lieux)';
   $posDs = positionEditionDS($dss,$ds);
  return  '(lieu '.$posDs. '/'.count($dss).')';

}

function progressionEdition($etape,$dss,$ds,$recap = false) 
{
    switch ($etape) {
        case 1:
            return 0;
        case 2:
            return 5;
        case 3:
            return getProgressionEtape3($dss,$ds,$recap);
        case 4:
            return 90;
        case 5:
            return 95;
        case 6:
            return 100;
        default:
            return $etape.' bizard';
            break;
    }
}

function getProgressionEtape3($dss,$ds,$recap = false) {
    $step_pourcent = 80 / count($dss);
    
    $courant_stock = ($ds->exist('courant_stock'))? $ds->courant_stock : null;
    $courant_id = preg_replace('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})-([A-Za-z0-9\_\-\/]*)/', '$1', $courant_stock);
    $hash_lieu = preg_replace('/^(DS-[0-9]{10}-[0-9]{6}-[0-9]{3})-([A-Za-z0-9\_\-\/]*)/', '$2', $courant_stock);
    if(!$courant_stock){
        return 10;
    }
    foreach (array_keys($dss) as $cpt => $id_ds) {
        if($id_ds == $courant_id){
            $ds_courante = $dss[$courant_id];
            $nbLieux = $ds_courante->nbLieuxEtape()+1;
            $hashesLieuxArray = $ds_courante->getLieuxHashSteps();
            $cpt_lieu_passe = 0;
            if($recap){
               return (int) (10+$cpt*$step_pourcent+ ($step_pourcent/$nbLieux)*($nbLieux-1));
            }
            foreach ($hashesLieuxArray as $cpt_lieu_key => $hashLieuxFromArray) {
                if($hashLieuxFromArray == $hash_lieu){
                    $cpt_lieu_passe = $cpt_lieu_key;
                    break;
                }
            }
            return (int) (10+$cpt*$step_pourcent+ ($step_pourcent/$nbLieux)*$cpt_lieu_passe);
        }
    }
    
    return (int) (10+$cpt*$step_pourcent);
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

function formatNumeroStockage($numero) {
    if(preg_match("/^([0-9]{10})([0-9]{3})$/", $numero, $matches)) {
        $numero = sprintf("%s&nbsp;%s", $matches[1], $matches[2]);
    }

    return $numero;
}

function getTitleLieuStockageStock($ds){

    return sprintf("Lieux de stockage : %s - %s, %s, %s %s", formatNumeroStockage($ds->stockage->numero), $ds->stockage->nom, $ds->stockage->adresse, $ds->stockage->code_postal, $ds->stockage->commune);
}

function isEtapePasse($etape,$ds){
    return ($etape < $ds->num_etape);
}


function getDateDeclaration($ds){
    if(substr($ds->periode,4) == '12'){
        return '31 décembre '.($ds->getCampagne());
    }
    return '31 Juillet '.($ds->getCampagne() + 1);
}


function getHeader($ds, $validee){
    $result = "";
    if($ds->isTypeDsNegoce()){
        $result = "Stocks Coopération et Négoce";
    }
    $result .= sprintf("\n%s", $ds->declarant->nom);
    if($ds->isTypeDsPropriete()){
       $result .= sprintf("\nCommune de déclaration : %s", $ds->declarant->commune); 
    }
   return $result.sprintf("\n%s", $validee);
}

function getHeaderBrouillon($ds, $validee){
    $result = "Ce document est une aide à la saisie pour la télédéclaration\n";
    $result .= "/!\\ Il ne doit pas être envoyé au CIVA";

   return $result;
}