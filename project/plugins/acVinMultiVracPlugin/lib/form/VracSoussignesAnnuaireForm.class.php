<?php
class VracSoussignesAnnuaireForm extends VracSoussignesForm
{
	public function configure()
    {
    	$this->disableCSRFProtection();
    	$types = $this->getTypes();
    	$contratTypes = $this->getContratTypes();
    	$recoltantChoices = $this->getRecoltants();
    	$negociantChoices = $this->getNegociants();
    	$caveCooperativeChoices = $this->getCavesCooperatives();
    	$commerciauxChoices = $this->getCommerciaux();
        $this->setWidgets(array(
            'type_contrat' => new sfWidgetFormChoice(array('choices' => $contratTypes, 'expanded' => true)),
        	'acheteur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'vendeur_type' => new sfWidgetFormChoice(array('choices' => $types, 'expanded' => true)),
        	'acheteur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'acheteur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_recoltant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($recoltantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_negociant_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($negociantChoices, array('add' => 'Ajouter un contact')))),
        	'vendeur_cave_cooperative_identifiant' => new sfWidgetFormChoice(array('choices' => array_merge($caveCooperativeChoices, array('add' => 'Ajouter un contact')))),
        	'interlocuteur_commercial' => new sfWidgetFormChoice(array('choices' => array_merge($commerciauxChoices, array('add' => 'Ajouter un contact'))))
    	));
        $this->setValidators(array(
        	'type_contrat' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($contratTypes))),
        	'acheteur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'vendeur_type' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($types))),
        	'acheteur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($recoltantChoices, array('add' => ''))))),
        	'acheteur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($negociantChoices, array('add' => ''))))),
        	'acheteur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($caveCooperativeChoices, array('add' => ''))))),
        	'vendeur_recoltant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($recoltantChoices, array('add' => ''))))),
        	'vendeur_negociant_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($negociantChoices, array('add' => ''))))),
        	'vendeur_cave_cooperative_identifiant' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($caveCooperativeChoices, array('add' => ''))))),
        	'interlocuteur_commercial' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys(array_merge($commerciauxChoices, array('add' => '')))))
        ));
        if (!$this->getObject()->isNew()) {
        	unset($this['type_contrat']);
        }
        $this->validatorSchema->setPostValidator(new VracSoussignesAnnuaireValidator($this->getObject()));
        $this->widgetSchema->setNameFormat('vrac_soussignes[%s]');
    }

    protected function doUpdateObject($values) 
    {
    	$acheteur = $values['acheteur'];
    	$vendeur = $values['vendeur'];
    	if ($values['interlocuteur_commercial']) {
    		if ($this->annuaire->commerciaux->exist($values['interlocuteur_commercial'])) {
	    		$this->getObject()->storeInterlocuteurCommercialInformations(
                                        $values['interlocuteur_commercial'],
                                        $this->annuaire->commerciaux->get($values['interlocuteur_commercial']));
    		}
    	}
    	$this->getObject()->acheteur_type = $values['acheteur_type'];
    	$this->getObject()->vendeur_type = $values['vendeur_type'];
    	if ($acheteur) {
    		$this->getObject()->acheteur_identifiant = $acheteur->_id;
    		$this->getObject()->storeAcheteurInformations($acheteur);
    	}
    	if ($vendeur) {
	    	$this->getObject()->vendeur_identifiant = $vendeur->_id;
	    	$this->getObject()->storeVendeurInformations($vendeur);
    	}
    	if ($this->getObject()->isNew()) {
    		$this->getObject()->type_contrat = $values['type_contrat'];
    	}
    }
    
    protected function getContratTypes()
    {
    	return VracClient::getContratTypes();
    }
    
	public function getUpdatedVrac()
  	{
  		$this->doUpdateObject($this->getValues());
    	return $this->getObject();
  	}
}