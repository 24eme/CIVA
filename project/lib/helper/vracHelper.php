<?php 

function renderTiersLibelle($tiers) {
	$libelle = '';
    if($tiers->intitule) {
    	$libelle .= $tiers->intitule.' ';	
    }
    $libelle .= $tiers->raison_sociale;
    return $libelle;
}