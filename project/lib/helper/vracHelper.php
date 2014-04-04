<?php 
/*
 * NECESSITE L'INCLUSION DU HELPER "TEXT"
 */
function renderTiersLibelle($tiers) {
	$libelle = '';
    if($tiers->intitule) {
    	$libelle .= $tiers->intitule.' ';	
    }
    $libelle .= $tiers->raison_sociale;
    return truncate_text($libelle, 35, '...', true);
}

function renderProduitIdentifiant($detail) {
	return str_replace('/', '_', $detail->getCepage()->getHash());
}