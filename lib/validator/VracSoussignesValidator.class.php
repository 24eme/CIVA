<?php
class VracSoussignesValidator extends sfValidatorBase 
{
    
    public function configure($options = array(), $messages = array()) 
    {
    	$this->setMessage('required', "Champ obligatoire");
        $this->setMessage('invalid', "Ce tiers n'existe plus");
        $this->addMessage('inconsistent', "L'acheteur et le vendeur ne peuvent être les mêmes");
    }

    protected function doClean($values) 
    {
    	$errorSchema = new sfValidatorErrorSchema($this);
    	$hasErrors = false;
    	$vendeur = null;
    	$acheteur = null;
    	$vendeur_type = (isset($values['vendeur_type']) && !empty($values['vendeur_type']))? str_replace('s', '', $values['vendeur_type']) : null;
    	$acheteur_type = (isset($values['acheteur_type']) && !empty($values['acheteur_type']))? str_replace('s', '', $values['acheteur_type']) : null;
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
    	if (!$vendeur) {
    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('vendeur_'.$vendeur_type.'_identifiant' => new sfValidatorError($this, 'required'))));
            $hasErrors = true;
    	} else {
    		$vendeur = $this->getTiers($vendeur);
	    	if (!$vendeur) {
	    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('vendeur_'.$vendeur_type.'_identifiant' => new sfValidatorError($this, 'invalid'))));
                $hasErrors = true;
	    	}
    	}
    	if (!$acheteur) {
    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('acheteur_'.$acheteur_type.'_identifiant' => new sfValidatorError($this, 'required'))));
            $hasErrors = true;
    	} else {
    		$acheteur = $this->getTiers($acheteur);
	    	if (!$acheteur) {
	    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('acheteur_'.$acheteur_type.'_identifiant' => new sfValidatorError($this, 'invalid'))));
	    		$hasErrors = true;
	    	}
    	}
        if ($hasErrors) {
        	throw $errorSchema;
        }
        if ($vendeur->_id == $acheteur->_id) {
        	throw new sfValidatorErrorSchema($this, array(new sfValidatorError($this, 'inconsistent')));
        }
        return array_merge($values, array('acheteur' => $acheteur, 'vendeur' => $vendeur));
    }
    
    protected function getTiers($id)
    {
    	return _TiersClient::getInstance()->find($id);
    }
}