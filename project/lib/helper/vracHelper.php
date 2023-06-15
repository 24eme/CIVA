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
	return str_replace('/', '_', $detail->getHash());
}

function isVersionnerCssClass($object, $key) {
    return (isVersionner($object, $key))? 'versionner' : null;
}

function isVersionner($object, $key) {
    return !$object->getDocument()->isValide() && $object->getDocument()->isModifiedMother($object->getHash(), $key);
}

function displayMail($messages) {
    if(!is_array($messages)) {
        $messages = [$messages];
    }

    $content = "";
    foreach($messages as $message) {
        $content .= "De : ".implode(",", array_keys($message->getFrom()))."\n";
        $content .= "À : ".implode(",", array_keys($message->getTo()))."\n";
        $content .= "Sujet : ".$message->getSubject()."\n\n";
        $content .= $message->getBody();
        $content .= "\n\n";
    }

    return $content;
}
