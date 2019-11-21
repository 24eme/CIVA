<?php
class VracSoussignesValidator extends sfValidatorBase
{

	protected $vrac;

	public function __construct($vrac, $options = array(), $messages = array())
  	{
  		$this->vrac = $vrac;
  		parent::__construct($options, $messages);
  	}

    public function configure($options = array(), $messages = array())
    {
    	$this->setMessage('required', "Champ obligatoire");
        $this->setMessage('invalid', "Cet opérateur n'est plus actif");
        $this->addMessage('inconsistent', "L'acheteur et le vendeur ne peuvent être les mêmes");
        $this->addMessage('email', "Des informations obligatoires sont manquantes");
    }

    protected function doClean($values)
    {
    	$errorSchema = new sfValidatorErrorSchema($this);
    	$hasErrors = false;
    	$vendeur = null;
    	$acheteur = null;
    	$courtier = null;
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
    	if (!$vendeur && $this->vrac->isVendeurProprietaire()) {
    		$vendeur = $this->vrac->vendeur_identifiant;
    	}

    	if (!$vendeur) {
    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('vendeur_'.$vendeur_type.'_identifiant' => new sfValidatorError($this, 'required'))));
            $hasErrors = true;
    	} else {
    		$vendeur = $this->getTiers($vendeur);
	    	if (!$vendeur || !$vendeur->isActif()) {
	    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('vendeur_'.$vendeur_type.'_identifiant' => new sfValidatorError($this, 'invalid'))));
                $hasErrors = true;
	    	}
    	}
    	if (!$acheteur && $this->vrac->isAcheteurProprietaire()) {
    		$acheteur = $this->vrac->acheteur_identifiant;
    	}
    	if (!$acheteur) {
    		$errorSchema->addError(new sfValidatorErrorSchema($this, array('acheteur_'.$acheteur_type.'_identifiant' => new sfValidatorError($this, 'required'))));
            $hasErrors = true;
    	} else {
    		$acheteur = $this->getTiers($acheteur);
	    	if (!$acheteur || !$acheteur->isActif()) {
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

        $this->vrac->storeVendeurInformations($vendeur);
        $this->vrac->storeAcheteurInformations($acheteur);

        $vendeurHasEmail = (count($this->vrac->vendeur->emails))? true : false;
        $acheteurHasEmail = (count($this->vrac->acheteur->emails))? true : false;
        $mandataireHasEmail = ($this->vrac->mandataire_identifiant && count($this->vrac->mandataire->emails))? true : false;

        if (!$this->vrac->isPapier() && (!$vendeurHasEmail || !$acheteurHasEmail)) {
        	throw new sfValidatorErrorSchema($this, array(new sfValidatorError($this, 'email')));
        }
        if (!$this->vrac->isPapier() && (!$this->vrac->isAcheteurProprietaire() && !$this->vrac->isVendeurProprietaire() && !$mandataireHasEmail)) {
        	throw new sfValidatorErrorSchema($this, array(new sfValidatorError($this, 'email')));
        }
        return array_merge($values, array('acheteur' => $acheteur, 'vendeur' => $vendeur));
    }

    protected function getTiers($id)
    {
    	return EtablissementClient::getInstance()->find($id);
    }
}
