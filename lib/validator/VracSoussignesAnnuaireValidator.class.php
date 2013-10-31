<?php
class VracSoussignesAnnuaireValidator extends VracSoussignesValidator 
{

    protected function doClean($values) 
    {
    	$errorSchema = new sfValidatorErrorSchema($this);
    	$hasErrors = false;
    	$vendeur = null;
    	$acheteur = null;
    	$vendeur_type = (isset($values['vendeur_type']) && !empty($values['vendeur_type']))? str_replace('s', '', $values['vendeur_type']) : null;
    	$acheteur_type = (isset($values['acheteur_type']) && !empty($values['acheteur_type']))? str_replace('s', '', $values['acheteur_type']) : null;
    	
    	if (isset($values['acheteur_recoltant_identifiant']) && $values['acheteur_recoltant_identifiant'] == 'add') {
    		$values['acheteur_recoltant_identifiant'] = null;
    	}
    	if (isset($values['acheteur_negociant_identifiant']) && $values['acheteur_negociant_identifiant'] == 'add') {
    		$values['acheteur_negociant_identifiant'] = null;
    	}
    	if (isset($values['acheteur_cave_cooperative_identifiant']) && $values['acheteur_cave_cooperative_identifiant'] == 'add') {
    		$values['acheteur_cave_cooperative_identifiant'] = null;
    	}
    	if (isset($values['vendeur_recoltant_identifiant']) && $values['vendeur_recoltant_identifiant'] == 'add') {
    		$values['vendeur_recoltant_identifiant'] = null;
    	}
    	if (isset($values['vendeur_negociant_identifiant']) && $values['vendeur_negociant_identifiant'] == 'add') {
    		$values['vendeur_negociant_identifiant'] = null;
    	}
    	if (isset($values['vendeur_cave_cooperative_identifiant']) && $values['vendeur_cave_cooperative_identifiant'] == 'add') {
    		$values['vendeur_cave_cooperative_identifiant'] = null;
    	}
    	if (isset($values['interlocuteur_commercial']) && $values['interlocuteur_commercial'] == 'add') {
    		$values['interlocuteur_commercial'] = null;
    	}
    	
    	if (isset($values['vendeur_recoltant_identifiant']) && !empty($values['vendeur_recoltant_identifiant'])) {
    		$vendeur = $values['vendeur_recoltant_identifiant'];
    	}
    	if (isset($values['vendeur_negociant_identifiant']) && !empty($values['vendeur_negociant_identifiant'])) {
    		$vendeur = $values['vendeur_negociant_identifiant'];
    	}
    	if (isset($values['vendeur_cave_cooperative_identifiant']) && !empty($values['vendeur_cave_cooperative_identifiant'])) {
    		$vendeur = $values['vendeur_cave_cooperative_identifiant'];
    	}
    	if (isset($values['acheteur_recoltant_identifiant']) && !empty($values['acheteur_recoltant_identifiant'])) {
    		$acheteur = $values['acheteur_recoltant_identifiant'];
    	}
    	if (isset($values['acheteur_negociant_identifiant']) && !empty($values['acheteur_negociant_identifiant'])) {
    		$acheteur = $values['acheteur_negociant_identifiant'];
    	}
    	if (isset($values['acheteur_cave_cooperative_identifiant']) && !empty($values['acheteur_cave_cooperative_identifiant'])) {
    		$acheteur = $values['acheteur_cave_cooperative_identifiant'];
    	}
    	if ($vendeur) {
    		$vendeur = $this->getTiers($vendeur);
    	}
    	if ($acheteur) {
    		$acheteur = $this->getTiers($acheteur);
    	}
        return array_merge($values, array('acheteur' => $acheteur, 'vendeur' => $vendeur));
    }
}